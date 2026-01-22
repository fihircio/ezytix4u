<?php

namespace Classiebit\Eventmie\Http\Controllers;

use Classiebit\Eventmie\Models\User;
use Classiebit\Eventmie\Models\Booking;

class InvoicesController extends Controller
{
    public $bookings = [];
    
    public function __construct($bookings = [])
    {
        if(empty($bookings))
            return true;

        $this->bookings = $bookings;

    }

    /**
     *  make invoice
     */
    public function makeInvoice()
    {
        $organizer = User::where(['id' => $this->bookings[key($this->bookings)]['organiser_id']])->first();

        //buyer
        $customer = User::where(['id' => $this->bookings[key($this->bookings)]['customer_id']])->first();
        
        $bookings = Booking::with(['attendees' => function ($query) {
            $query->where(['status' => 1]);
        }, 'attendees.seat'])->whereIn('id',collect($this->bookings)->pluck('id')->all() )->get();
        
        // resources\views\vendor\invoices\templates\default.blade.php
        $img_path   = str_replace('https://', 'http://', url(''));
        
        $pdf_html   = (string) \View::make('invoice.invoice', compact('bookings', 'organizer', 'customer', 'img_path'));
        
        $pdf_name   = 'invoices/'.$customer->id;
        
        $invoices =  $this->generatePdf($pdf_html, $pdf_name, $this->bookings[key($this->bookings)]);

        return $invoices;

    }

    /**
     *  generate pdf
     */
    public function generatePdf($html = null, $pdf_name = null, $data = [])
    {
        $path           = 'invoices/'.$data['customer_id'];
        $pdf_file       = $path.'/'.$data['common_order'].'-invoice.pdf';
        
        $disk = \Storage::disk(config('filesystems.default'));

        // remove white spaces and comments
        $html =  preg_replace('/>\s+</', '><', $html);
        if(empty($html))
            return false;

        $options = [
            'defaultFont' => 'sans-serif',
            'isRemoteEnabled' => TRUE,
            'isJavascriptEnabled' => FALSE,
            'debugKeepTemp' => TRUE,
            'isHtml5ParserEnabled' => TRUE,
            'enable_html5_parser' => TRUE,
        ];
        
        $pdf = \PDF::setOptions($options)
        ->loadHTML($html)
        ->setWarnings(false)
        ->setPaper('a4', 'portrait')
        ->output();

        $disk->put($pdf_file, $pdf);
        
        return $pdf_file;
    } 
}
