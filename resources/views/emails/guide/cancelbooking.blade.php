<!DOCTYPE html>
<html lang="en-US">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Most Popular Tours in Bali – Hire Bali Driver</title>
   </head>
   <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="padding: 0;">
      <div id="wrapper" dir="ltr" style="background-color: #f7f7f7; margin: 0; padding: 70px 0; width: 100%; -webkit-text-size-adjust: none;" bgcolor="#f7f7f7" width="100%">
         <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            <tr>
               <td align="center" valign="top">

                  <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="background-color: #fff; border: 1px solid #dedede; box-shadow: 0 1px 4px rgba(0,0,0,.1); border-radius: 3px;" bgcolor="#fff">
                     <tr>
                        <td align="center" valign="top">
                           <!-- Header -->
                           <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header" style='background-color: #8b0707; color: #fff; border-bottom: 0; font-weight: bold; line-height: 100%; vertical-align: middle; font-family: "Times New Roman", sans-serif; border-radius: 3px 3px 0 0;' bgcolor="#077944">
                              <tr>
                                 <td id="header_wrapper" style="padding: 36px 48px; display: block;">
                                    <h1 style='font-family: "Times New Roman", sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left; text-shadow: 0 1px 0 #770c0c; color: #fff; background-color: inherit;' bgcolor="inherit">Cancelled Tour {{$details['ref']}}</h1>
                                 </td>
                              </tr>
                           </table>
                           <!-- End Header -->
                        </td>
                     </tr>
                     <tr>
                        <td align="center" valign="top">
                           <!-- Body -->
                           <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                              <tr>
                                 <td valign="top" id="body_content" style="background-color: #fff;" bgcolor="#fff">
                                    <!-- Content -->
                                    <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                       <tr>
                                          <td valign="top" style="padding: 48px 48px 32px;">
                                             <div id="body_content_inner" style='color: #636363; font-family: "Times New Roman", sans-serif; font-size: 14px; line-height: 150%; text-align: left;' align="left">
                                                <p style="margin: 0 0 5px;">Hi Mr.{{$details['name']}},</p>
                                                <h2 style='color: #077944; display: block; font-family: "Times New Roman", sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;'>
                                                   {{$details['ref']}}
                                                </h2>
                                                <h2 style='color: #2f3134; display: block; font-family: "Times New Roman", sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;'>
                                                    {{$details['package']}}
                                                </h2>
                                                <p style="margin: 0 0 5px;"><strong>Option:</strong> {{$details['option']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Date:</strong> {{$details['date']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Time:</strong> {{$details['time']}}</p>
                                                 <p style="margin: 0 0 5px;"><strong>Name:</strong> {{$details['guestName']}}</p>
                                                 <p style="margin: 0 0 5px;"><strong>Nationality:</strong> {{$details['country']}}</p>
                                                 <p style="margin: 0 0 5px;"><strong>Adult:</strong> {{$details['adult']}}</p>
                                                 @if($details['child'] != 0)
                                                    <p style="margin: 0 0 5px;"><strong>Child:</strong> {{$details['child']}}</p>
                                                 @endif
                                                 <p style="margin: 0 0 5px;"><strong>Phone:</strong> {{$details['phone']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Hotel:</strong> {{$details['hotel']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Payment:</strong> {{$details['status_payment']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Collect:</strong> USD {{$details['collect']}}</p>
                                                <p style="margin: 0 0 5px;"><strong>Special requirements:</strong> {{$details['note']}}</p>
                                                
                                               
                                             </div>
                                          </td>
                                       </tr>
                                    </table>
                                    <!-- End Content -->
                                 </td>
                              </tr>
                           </table>
                           <!-- End Body -->
                        </td>
                     </tr>
                  </table>
               </td>
            </tr>
            <tr>
               <td align="center" valign="top">
                  <!-- Footer -->
                  <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
                     <tr>
                        <td valign="top" style="padding: 0; border-radius: 6px;">
                           <table border="0" cellpadding="10" cellspacing="0" width="100%">
                              <tr>
                                 <td colspan="2" valign="middle" id="credit" style='border-radius: 6px; border: 0; color: #8a8a8a; font-family: "Times New Roman", sans-serif; font-size: 12px; line-height: 150%; text-align: center; padding: 24px 0;' align="center">
                                    <p style="margin: 0 0 5px;">Most Popular Tours in Bali – Hire Bali Driver<br>Powered by <a href="http://hirebalidriver.com/" style="color: #077944; font-weight: normal; text-decoration: underline;">Hire Bali Driver</a></p>
                                 </td>
                              </tr>
                           </table>
                        </td>
                     </tr>
                  </table>
                  <!-- End Footer -->
               </td>
            </tr>
         </table>
      </div>
   </body>
</html>
