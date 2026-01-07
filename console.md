--
FORM DATA
--
event_id
1
booking_date
2026-01-08
booking_end_date
start_time
16:00:00
end_time
18:30:00
merge_schedule
0
customer_id
0
ticket_id[]
1
ticket_title[]
Free
seat_id_1[]
5
name_0_0
Test Company
phone_0_0
123123123
address_0_0
test
promocode[]
ticket_id[]
2
ticket_title[]
Early Bird
quantity[]
1
name_1_0
Test Company
phone_1_0
0123456789
address_1_0
t
promocode[]
ticket_id[]
3
ticket_title[]
Regular
quantity[]
1
name_2_0
Test Company
phone_2_0
9876543210
address_2_0
test
promocode[]
ticket_id[]
4
ticket_title[]
VIP
quantity[]
0
promocode[]
payment_method
11
name
[["Test Company"],["Test Company"],["Test Company"],[],[],[]]
phone
[["123123123"],["0123456789"],["9876543210"],[],[],[]]
address
[["test"],["t"],["test"],[],[],[]]

--
PREVIEW
--
array:25 [
  "event_id" => "1"
  "booking_date" => "2026-01-08"
  "booking_end_date" => null
  "start_time" => "16:00:00"
  "end_time" => "18:30:00"
  "merge_schedule" => "0"
  "customer_id" => "0"
  "ticket_id" => array:4 [
    0 => "1"
    1 => "2"
    2 => "3"
    3 => "4"
  ]
  "ticket_title" => array:4 [
    0 => "Free"
    1 => "Early Bird"
    2 => "Regular"
    3 => "VIP"
  ]
  "seat_id_1" => array:1 [
    0 => "5"
  ]
  "name_0_0" => "Test Company"
  "phone_0_0" => "123123123"
  "address_0_0" => "test"
  "promocode" => array:4 [
    0 => null
    1 => null
    2 => null
    3 => null
  ]
  "quantity" => array:3 [
    0 => "1"
    1 => "1"
    2 => "0"
  ]
  "name_1_0" => "Test Company"
  "phone_1_0" => "0123456789"
  "address_1_0" => "t"
  "name_2_0" => "Test Company"
  "phone_2_0" => "9876543210"
  "address_2_0" => "test"
  "payment_method" => "11"
  "name" => "[["Test Company"],["Test Company"],["Test Company"],[],[],[]]"
  "phone" => "[["123123123"],["0123456789"],["9876543210"],[],[],[]]"
  "address" => "[["test"],["t"],["test"],[],[],[]]"
]