<?php

namespace Plainternet\Bundle\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $this->getContainer()->get('fos_user.user_manager')->findUserByUsername('moein');
        $f = $this->getContainer()->get('plainternet_file_manager.file_processor');
        $f->batchProcess('/tmp/photos.zip', $user);
    }
}