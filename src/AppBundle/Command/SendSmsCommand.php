<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 9/4/17
 * Time: 8:46 AM
 */

namespace AppBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class SendSmsCommand extends Command
{

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct('smpp:send:sms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $smpp = $this->container->get('kronas_smpp_client.transmitter');
        $smpp->send('380936538334', 'КИЇВСТАР Батьківській контроль. Код доступу 000000');
    }
}