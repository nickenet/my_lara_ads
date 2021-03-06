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

class AdPosted extends Mailable
{
    use Queueable, SerializesModels;

    public $ad;

    /**
     * Create a new message instance.
     *
     * @param Ad $ad
     */
    public function __construct(Ad $ad)
    {
        $this->ad = $ad;

        $this->to($ad->seller_email, $ad->seller_name);
        $this->subject(trans('mail.Activate your ad ":title"', ['title' => str_limit($ad->title, 50)]));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ad.posted');
    }
}
