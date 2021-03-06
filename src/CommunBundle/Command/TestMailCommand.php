<?php

namespace CommunBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Commande permettant de tester que l'envoi de mail est ok
 * Class TestMailCommand
 */
class TestMailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mail:send')
            ->setDescription("Test l'envoi de mail");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mailerTo = $this->getContainer()->getParameter('destinataire_mail');
        if (!is_null($mailerTo)) {
            $now     = new \DateTime();
            $message = \Swift_Message::newInstance("Test de mail  : " . $now->format('d/m/Y H:i'))
                ->setFrom($this->getContainer()->getParameter('mailer_from'))
                ->setTo($mailerTo)
                ->setBody('Done', 'text/html', 'utf-8');
            $this->getContainer()->get('mailer')->send($message);
        }


    }


}