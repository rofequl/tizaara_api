<?php

namespace App\Http\Controllers;

use App\GeneralSetting;
use App\Subscriber;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function newsletterIndex()
    {
        $collection = collect();
        $subscriber = collect();
        $supplier = collect();
        $buyer = collect();
        $both = collect();
        foreach (Subscriber::all()->pluck('email') as $email) {
            $subscriber->push($email);
        }

        foreach (User::where('status', 2)->select('email', 'account_type')->get() as $email) {
            if ($email->account_type == 0) {
                $both->push($email->email);
            } elseif ($email->account_type == 1) {
                $supplier->push($email->email);
            } else {
                $buyer->push($email->email);
            }

        }

        $collection->push([
            'title' => 'Subscribers',
            'elements' => $subscriber
        ], [
            'title' => 'Supplier',
            'elements' => $supplier
        ], [
            'title' => 'Buyer',
            'elements' => $buyer
        ], [
            'title' => 'Both',
            'elements' => $both
        ]);

        return $collection;

    }

    public function newsletterStore(Request $request)
    {
        if (env('MAIL_USERNAME') != null) {
            foreach ($request->email as $email) {
                $content = $request->message;
                $subject = $request->subject;
                $site_name = GeneralSetting::all()->first()->pluck('site_name');
                try {
                    Mail::send('email.newsletter', ['content' => $content, 'site_name' => $site_name[0]],
                        function ($mail) use ($email, $subject, $site_name) {
                            $mail->from(env('MAIL_USERNAME'), $site_name[0]);
                            $mail->to($email);
                            $mail->subject($subject);
                        });
                } catch (\Exception $e) {
                    return $e;
                }
                return 'Done';
            }
        } else {
            return '';
        }
    }

    public function subscribeStore(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|max:255|unique:subscribers',
        ]);

        return Subscriber::create($request->all());
    }
}
