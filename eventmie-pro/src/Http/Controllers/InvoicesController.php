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
        $path           = '/storage/invoices/'.$data['customer_id'];
        
        // first check if directory exists or not
        if (! \File::exists(public_path().$path))
            \File::makeDirectory(public_path().$path, 0755, true);

        $pdf_file    = public_path('storage/'.$pdf_name.'/'.$data['common_order'].'-invoice.pdf');

        // only create if not already created
        // if (\File::exists($pdf_file))
        //     return TRUE;
            
        // start PDF generation

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
        \PDF::setOptions($options)
        ->loadHTML($html)
        ->setWarnings(false)
        ->setPaper('a4', 'portrait')
        ->save($pdf_file);
        
        return $pdf_file;
    } 
}
