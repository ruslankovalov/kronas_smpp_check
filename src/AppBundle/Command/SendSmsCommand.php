<?php
/**
 * Created by PhpStorm.
 * User: ruslan
 * Date: 9/4/17
 * Time: 8:46 AM
 */

namespace AppBundle\Command;


use Kronas\SmppClientBundle\Encoder\GsmEncoder;
use Kronas\SmppClientBundle\Service\SmppTransmitter;
use Kronas\SmppClientBundle\SMPP;
use Kronas\SmppClientBundle\SmppCore\SmppAddress;
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
        parent::__construct('smpp:send:sms');
    }

    protected function configure()
    {
        $this->addArgument('phone', InputArgument::REQUIRED, 'Receiver phone number');
        $this->addArgument('message', InputArgument::OPTIONAL, 'The message itself', 'test message');
        $this->addArgument('operator', InputArgument::OPTIONAL, 'kyivstar or vip', 'kyivstar');
        $this->addArgument('sender', InputArgument::OPTIONAL, 'This is sender name', 'testsms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phone = $input->getArgument('phone');
        $operator = $input->getArgument('operator');
        $message = $input->getArgument('message');
        $sender = $input->getArgument('sender');
        if ($operator == 'kyivstar') {
            $smpp = new SmppTransmitter([['5.178.83.10'], 2778, 1000], 'kidslox', 'GZpnO5H4', 'testsms', ['transport' => true, 'smpp' => true]);
        } elseif ($operator == 'vip') {
            $smpp = new SmppTransmitter([['77.243.16.54'], 2775, 1000], 'kids-lox', '6wbr-4', 'testsms', ['transport' => true, 'smpp' => true]);
        } else {
            throw new InvalidArgumentException('There is no place for "else". Operators should be very specific.');
        }

        //TODO Fork EitherSoft/SmppClientBundle (kronas/smpp-client-bundle) and make openSmppConnection closeSmppConnection public and add getSmpp
//        $message = 'КИЇВСТАР Батьківській контроль. Код доступу 000000';

        $message = GsmEncoder::utf8_to_gsm0338($message);
//        $message = mb_convert_encoding($message, 'ISO-8859-5', 'UTF-8');
//        $message = mb_convert_encoding($message, 'UTF-8', 'ISO-8859-5');
        $from = new SmppAddress($sender, SMPP::TON_ALPHANUMERIC);
        $to = new SmppAddress(intval($phone), SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);

        $smpp->openSmppConnection();
        $messageId = $smpp->getSmpp()->sendSMS($from, $to, $message, null, 0);
        $output->writeln('Message ID: '.$messageId);
        $smpp->closeSmppConnection();
//        $smpp->send('380936538334', 'КИЇВСТАР Батьківській контроль. Код доступу 000000');
    }
}