<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            "purchase.success" => "sendSuccessEmail"
        ];
    }

    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }


    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {

        /**
         * @var User
         */
        $currentUser = $this->security->getUser();

        $purchase = $purchaseSuccessEvent->getPurchase();

        $email = new TemplatedEmail();
        $email
            ->to(new Address($currentUser->getEmail()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate("emails/purchase_success.html.twig")
            ->context([
                "purchase" => $purchase,
                "user" => $currentUser
            ]);

        $this->mailer->send($email);

        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}