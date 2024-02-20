<?php
namespace App\Enums;

// documentation sourced from: https://mrcoles.com/stripe-api-subscription-status/
enum SubscriptionStatusEnum:Int {
    case INCOMPLETE = 1; // this is for the very first payment of a subscription. Depending on the payment_behavior set for your subscription and if the payment fails or requires additional steps (like 3-D Secure cards), then a subscription will have this status.
    
    case INCOMPLETE_EXPIRED = 2; // if the subscription is in incomplete for 23 hours, then the customer is not billed and the subscription is effectively canceled and goes to this status.
    
    case TRIAL = 3; // if the subscription has a trial, it will start with this status until the end of the trial (there is also a non-payment invoice created for the trial).
    
    case ACTIVE = 4; // the subscription is in good standing.
    
    case PAST_DUE = 5; // the most recent invoice (other than the very first, subscription creation invoice) failed or hasn’t been attempted. You can manage your failed payment retries and rules in the Stripe Billing dashboard.
    
    case CANCELLED = 6; // the subscription has been canceled. You’ll encounter an error if you try to update it. Your retry rules can be set up to move failed retries into this status.
    
    case UNPAID = 7; // this is an alternative to canceled and leaves invoices open, but doesn’t attempt to pay them until a new payment method is added.

    public function humanreadablestring(): string
    {
           return match($this) {
               SubscriptionStatusEnum::INCOMPLETE => 'Incomplete', 
               SubscriptionStatusEnum::INCOMPLETE_EXPIRED => 'Incomplete Expired', 
               SubscriptionStatusEnum::TRIAL => 'Trial', 
               SubscriptionStatusEnum::ACTIVE => 'Active',
               SubscriptionStatusEnum::PAST_DUE => 'Past Due',
               SubscriptionStatusEnum::CANCELLED => 'Cancelled',
               SubscriptionStatusEnum::UNPAID => 'Unpaid'
           };    
    }
    
}