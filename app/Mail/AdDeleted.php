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

class AdDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public $ad;

    /**
     * Create a new message instance.
     *
     * @param $ad
     */
    public function __construct($ad)
    {
        $this->ad = $ad;

        $this->to($ad->seller_email, $ad->seller_name);
        $this->subject(trans('mail.Your ad ":title" has been deleted', ['title' => $ad->title]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ad.deleted');
    }
}
