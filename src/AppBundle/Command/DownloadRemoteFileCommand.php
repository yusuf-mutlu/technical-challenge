<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class DownloadRemoteFileCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('app:downloadRemoteFile')
            ->setDescription('Download Remote File')
            ->addArgument("download_file_url")
            ->addArgument("download_destination_path")
            ->addArgument("downloaded_file_name");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute($input, $output)
    {

        $downloadUrl = $input->getArgument("download_file_url");

        $downloadDestinationPath = $input->getArgument("download_destination_path");
        if (!is_dir($downloadDestinationPath))
        {
            mkdir($downloadDestinationPath, 0777, true);
        }

        //Disable temporary SSL verification
        $arrContextOptions = array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            )
        );

        $context = stream_context_create($arrContextOptions, array('notification' => function ($notification_code) use ($output) {
            switch ($notification_code) {
                case STREAM_NOTIFY_RESOLVE:
                case STREAM_NOTIFY_AUTH_REQUIRED:
                case STREAM_NOTIFY_COMPLETED:
                case STREAM_NOTIFY_FAILURE:
                case STREAM_NOTIFY_PROGRESS:
                case STREAM_NOTIFY_REDIRECTED:
                case STREAM_NOTIFY_AUTH_RESULT:
                    break;

                case STREAM_NOTIFY_CONNECT:
                    $output->writeln("Connected.");
                    break;
                case STREAM_NOTIFY_FILE_SIZE_IS:
                    $output->writeln("Downloading...");
                    break;
            }
        }));

        //Download file
        $streamContent = file_get_contents($downloadUrl,false,$context);

        //Save File
        file_put_contents($downloadDestinationPath.'/'.$input->getArgument("downloaded_file_name"), $streamContent);

        $output->writeln("Download has finished.");
        $output->writeln("Downloaded file path:");
        $output->writeln($downloadDestinationPath.'/'.$input->getArgument("downloaded_file_name"));
    }
}