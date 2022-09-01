<html>

<body style="background-color:#e2e1e0;font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;">
    <table style="max-width:670px;margin:50px auto 10px;background-color:#fff;padding:50px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);-moz-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); border-top: solid 10px green;">
        <thead>
            <tr>
                <th style="text-align:center;"><img style="max-width: 150px;" src="{{ URL::asset('admin_assets/img/logo.png')}} alt="bachana tours"></th>
            </tr>
            <hr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Customer Name:&nbsp;&nbsp;</span>{{$username}} </p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Customer Email:&nbsp;&nbsp;</span>{{$email}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Booking ID:&nbsp;&nbsp;</span>{{$booking_id}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Studio Name:&nbsp;&nbsp;</span>{{$title}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Booking Date:&nbsp;&nbsp;</span>${{$amount}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Start Time:&nbsp;&nbsp;</span>{{$start_time}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">End Time:&nbsp;&nbsp;</span>{{$end_time}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Total Hour:&nbsp;&nbsp;</span>{{$total_hour}} Hour</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Address:&nbsp;&nbsp;</span>{{$address}}</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">city:&nbsp;&nbsp;</span>{{$city}}</p>
                </td>
            </tr>
            <hr>
            <hr>
            <h3 style="text-align:center;">Payment Receipt</h3>
            <hr>
            <tr>
                <td colspan="2">
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:350px">Fees:</span>${{$fees}}.00</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:350px">Per Hour Rate</span>${{$price}}.00</p>
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:350px">Discount:</span>${{$discount}}.00</p>
                </td>
            </tr>
            <hr>
            <tr>
                <td colspan="2">
                    <p style="font-size:14px;margin:0 0 2px 0;"><span style="font-weight:bold;display:inline-block;min-width:350px">Total Amount:</span style="color:green;font-weight:normal;margin:0">${{$amount}}.00</p>
                </td>
            </tr>
            <hr>
        </tbody>
        <tfooter>
            <tr>
                <td style="font-size:14px;">
                    <strong>Regards</strong>
                    <p>Studio Tonight</p>
                    <b>Email:</b>support@studiotonight.gmail.com
                </td>
            </tr>
        </tfooter>
    </table>
</body>

</html>