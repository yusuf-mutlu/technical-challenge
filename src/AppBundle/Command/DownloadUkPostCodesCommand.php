<?php

namespace AppBundle\Command;

use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class DownloadUkPostCodesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('app:downloadUkPostCodes')
            ->setDescription('Import UK Postcodes');
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
        $post_codes_download_url = "https://api.os.uk/downloads/v1/products/CodePointOpen/downloads?area=GB&format=CSV&redirect";
        $post_codes_download_destination_path = $this->getContainer()->get('kernel')->getProjectDir().'/data';
        $post_codes_downloaded_file_name  =  "uk_post_codes.zip";
        $post_codes_extracted_path = $post_codes_download_destination_path."/".basename($post_codes_downloaded_file_name,".zip");

        $command = $this->getApplication()->find('app:downloadRemoteFile');

        $arguments = [
            'download_file_url' => $post_codes_download_url,
            'download_destination_path' => $post_codes_download_destination_path,
            'downloaded_file_name' => $post_codes_downloaded_file_name
        ];

        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

        //extract the downloaded file
        $zip = new ZipArchive;
        if ($zip->open($post_codes_download_destination_path."/".$post_codes_downloaded_file_name) === TRUE)
        {

            if (!is_dir($post_codes_extracted_path))
            {
                mkdir($post_codes_extracted_path,0777,true);
            }

            $zip->extractTo($post_codes_extracted_path);
            $zip->close();
            $output->writeln("File extracted to: ".$post_codes_extracted_path);

        } else {
            $output->writeln("Extract failed!");
        }

        //delete the downloaded zip file
        if (!is_dir($post_codes_download_destination_path."/".$post_codes_downloaded_file_name))
        {
            unlink($post_codes_download_destination_path."/".$post_codes_downloaded_file_name);
            $output->writeln("Extracted zip file deleted!");
        }

        //start writing post codes to database
        $command = $this->getApplication()->find('app:importUkPostcodes');

        $arguments = [
            'post_codes_extracted_csv_path' => $this->getContainer()->get('kernel')->getProjectDir().'/data/uk_post_codes/Data/CSV/'
        ];

        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

    }

}