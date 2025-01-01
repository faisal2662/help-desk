 <!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">

 <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     <title>Helpdesk - Jamkrindo</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 </head>

 <body style="margin: 0; padding: 0;background-color: #ecf0f1;">
     <table border="0" cellpadding="0" cellspacing="0" width="100%">
         <tr>
             <td style="padding: 10px 0 30px 0;">
                 <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
                     style="background-color: #ffffff;border-radius: 10px;">
                     <tr>
                         <td align="center" bgcolor="#ffffff"
                             style="padding: 40px 0 30px 0; color: #ffffff; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
                             <img src="cid:logo_cid" style="width: 250px;">
                             {{-- <img src="{{ asset('logos/logo.png') }}" style="width: 250px;"> --}}
                         </td>
                     </tr>
                     <tr>

                         <td align="center" style="padding: 20px;">
                             <p style="font-size:13pt; ">Kode akses login untuk masuk ke Aplikasi Helpdesk, berlaku
                                 hanya 30
                                 menit </p>
                         </td>
               
                     </tr>
                     <tr>
                         <td align="center">
                             <h2 style="text-decoration: underline; letter-spacing:4px;">{{ $otpCode }}</h2>
                         </td>
                     </tr>
                     <tr>
                         <td align="center" style="padding:40px;">Silahkan kode diatas ke dalam halaman Verifikasi Otp
                             untuk bisa masuk kedalam Aplikasi <strong>Helpdesk - Jamkrindo</strong></td>
                     </tr>

                     <tr>
                         <td bgcolor="#188fff" style="padding: 30px 30px 30px 30px;">
                             <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                 <tr>
                                     <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;"
                                         width="75%">
                                         &copy; Copyright 2024 Helpdesk - Jamkrindo
                                     </td>
                                 </tr>
                             </table>
                         </td>
                     </tr>
                 </table>
             </td>
         </tr>
     </table>
 </body>

 </html>
