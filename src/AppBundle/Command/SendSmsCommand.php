<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 9/4/17
 * Time: 8:46 AM
 */

namespace AppBundle\Command;


use Kronas\SmppClientBundle\Transmitter\SmppTransmitter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
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
        parent::__construct('smpp:sms:send');
    }

    protected function configure()
    {
        $this->addArgument('phone', InputArgument::REQUIRED, 'Receiver phone number');
        $this->addArgument('message', InputArgument::OPTIONAL, 'The message itself', 'test message');
        $this->addArgument('operator', InputArgument::OPTIONAL, 'kyivstar or vip', 'kyivstar');
        $this->addArgument('sender', InputArgument::OPTIONAL, 'This is sender name', 'testsms');
        $this->addArgument('encoding', InputArgument::OPTIONAL, 'encoding "cyr" or "gsm"', 'gsm');
        $this->addArgument('data_coding', InputArgument::OPTIONAL, 'data_coding', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phone = $input->getArgument('phone');
        $message = $input->getArgument('message');
        $operator = $input->getArgument('operator');
        $sender = $input->getArgument('sender');
        $encoding = $input->getArgument('encoding');
        $dataCoding = $input->getArgument('data_coding');
        if ($operator == 'kyivstar') {
            $smpp = new SmppTransmitter(['5.178.83.10'], [2778], 1000, 'kidslox', 'GZpnO5H4', $sender, '', ['transport' => true, 'smpp' => true]);
        } elseif ($operator == 'vip') {
            $smpp = new SmppTransmitter(['77.243.16.54'], [2775], 1000, 'kids-lox', '6wbr-4', $sender, 'abc12cba',['transport' => true, 'smpp' => true]);
        } else {
            throw new InvalidArgumentException('There is no place for "else". Operators should be very specific.');
        }

        $smpp->send($phone, $message, $encoding, $dataCoding);
    }
}