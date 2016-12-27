<?php
/**
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) Mayeul Akpovi. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Larapen\Models\Ad;
use App\Larapen\Models\Message;

class SellerContacted extends Mailable
{
    use Queueable, SerializesModels;

    public $ad;
    public $msg;

    /**
     * Create a new message instance.
     *
     * @param Ad $ad
     * @param Message $msg
     */
    public function __construct(Ad $ad, Message $msg)
    {
        $this->ad = $ad;
        $this->msg = $msg;

        $this->to($ad->seller_email, $ad->seller_name);
        $this->replyTo($msg->email, $msg->name);
        $this->subject(trans('mail.Your ad ":title" on :app_name', [
            'title' => $ad->title,
            'app_name' => config('settings.app_name')
        ]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ad.seller-contacted');
    }
}
