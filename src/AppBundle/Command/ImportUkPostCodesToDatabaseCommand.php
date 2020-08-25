<?php

namespace AppBundle\Command;

use AppBundle\Helper\Convert;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\PostCode;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportUkPostCodesToDatabaseCommand extends ContainerAwareCommand {

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:importUkPostcodes')
            ->setDescription('Import UK Postcodes to database')
            ->addArgument("post_codes_extracted_csv_path");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws Exception
     */
    protected function execute($input, $output)
    {

        $post_codes_extracted_csv_path = $input->getArgument("post_codes_extracted_csv_path") ;

        foreach(glob($post_codes_extracted_csv_path.'*.csv') as $i => $file) {
            $output->writeln("Started writing postcodes to database. Please wait...");
            $output->writeln("(Press CTRL+C to exit processing. Some sample data will already be imported.)");
            $file = fopen($file, 'r');
            while (($line = fgetcsv($file)) !== FALSE)
            {

                $post_code_exist = $this->em->getRepository(PostCode::class)->findOneBy(['post_code' => $line[0]]);

                if($post_code_exist === NULL)
                {

                    //Convert OSGB easting and northing to latitude and longitude
                    $convert = new Convert();
                    $lat_long = $convert->osGridToLatLong($line[2], $line[3]);

                    $post_code = new PostCode();
                    $post_code->setPostCode($line[0]);
                    $post_code->setLatitude($lat_long->latitude);
                    $post_code->setLongitude($lat_long->longitude);

                    $this->em->getEventManager();
                    $this->em->persist($post_code);
                    $this->em->flush();
                }

            }
            fclose($file);
        }

        $output->writeln("All postcodes successfully inserted to database.");
    }

}