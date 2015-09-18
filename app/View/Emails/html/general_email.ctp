<?php $this->assign('title',$title_for_email); ?>

<center>
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
        <tr>
            <td align="center" valign="top" id="bodyCell">
                  <!-- BEGIN TEMPLATE // -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <tr>
                          <td align="center" valign="top">
                              <!-- BEGIN PREHEADER // -->
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templatePreheader">
                                  <tr>
                                    <td align="center" valign="top">
                                          <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                              <tr>
                                                  <td valign="top" class="preheaderContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock">
<tbody class="mcnDividerBlockOuter">
  <tr>
      <td class="mcnDividerBlockInner" style="padding: 18px;">
          <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%">
              <tbody><tr>
                  <td>
                      <span></span>
                  </td>
              </tr>
          </tbody></table>
      </td>
  </tr>
</tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
<tbody class="mcnTextBlockOuter">
  <tr>
      <td valign="top" class="mcnTextBlockInner">

          <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
              <tbody><tr>

                  <td valign="top" class="mcnTextContent" style="padding: 9px 18px;color: #3B0E0E;font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size: 36px;font-weight: bold;line-height: 100%;text-align: center;">

                      <div style="text-align: center;"><strong>Signzy</strong></div>

                  </td>
              </tr>
          </tbody></table>

      </td>
  </tr>
</tbody>
</table></td>
                                              </tr>
                                          </table>
                                      </td>
                                  </tr>
                              </table>
                              <!-- // END PREHEADER -->
                          </td>
                      </tr>
                      <tr>
                        <td align="center" valign="top">
            <!-- BEGIN HEADER // -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateHeader">
                                <tr>
                                    <td align="center" valign="top" style="padding-top:20px; padding-bottom:20px;">
                                          <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                    <tr>
                      <td align="center" height="10" valign="top" width="10">
                        <img src="https://gallery.mailchimp.com/27aac8a65e64c994c4416d6b8/images/d4042106-8117-4b79-b76b-91f8d64c5dff.gif" height="10" width="10" style="display:block; line-height:0px;">
                      </td>
                      <td align="center" height="10" valign="top" class="headerRearBackground" style="opacity:0.5;">
                        <img src="https://gallery.mailchimp.com/27aac8a65e64c994c4416d6b8/images/640a7ee0-db88-4905-a550-89e571c94697.png" class="mcnImage" height="10" width="580" style="display:block; line-height:0px;">
                      </td>
                      <td align="center" height="10" valign="top" width="10">
                        <img src="https://gallery.mailchimp.com/27aac8a65e64c994c4416d6b8/images/d4042106-8117-4b79-b76b-91f8d64c5dff.gif" height="10" width="10" style="display:block; line-height:0px;">
                      </td>
                    </tr>
                                            <tr>
                                                <td align="center" colspan="3" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="headerFrontBackground">
                                                          <tr>
                                                              <td align="center" valign="top">
                                                                  <!-- BEGIN HEADER // -->
                                                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" id="">
                                                                      <tr>
                                                                          <td valign="top" class="headerContainer" style="padding-top:20px; padding-bottom:20px;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
<tbody class="mcnTextBlockOuter">
  <tr>
      <td valign="top" class="mcnTextBlockInner">

          <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
              <tbody><tr>

                  <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">

                      <h1>Hello , <?php echo $name_of_user ?></h1>

                  </td>
              </tr>
          </tbody></table>

      </td>
  </tr>
</tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
<tbody class="mcnTextBlockOuter">
  <tr>
      <td valign="top" class="mcnTextBlockInner">

          <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
              <tbody><tr>

                  <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">

                      <?php echo $content_for_email;?>
                  </td>
              </tr>
          </tbody></table>

      </td>
  </tr>
</tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnButtonBlock">
<tbody class="mcnButtonBlockOuter">
  <tr>
      <td style="padding-top:0; padding-right:18px; padding-bottom:18px; padding-left:18px;" valign="top" align="center" class="mcnButtonBlockInner">
          <table border="0" cellpadding="0" cellspacing="0" class="mcnButtonContentContainer" style="border-collapse: separate !important;border: 2px solid #F2F2F2;border-radius: 4px;background-color: #FFFFFF;">
              <tbody>
                  <tr>
                      <td align="center" valign="middle" class="mcnButtonContent" style="font-family: Arial; font-size: 16px; padding: 20px;">
                          <a class="mcnButton " title="Confirm my Email" href="<?php echo $link ?>" target="_blank" style="font-weight: bold;letter-spacing: 1px;line-height: 100%;text-align: center;text-decoration: none;color: #D55258;"><?php echo $button_text; ?></a>
                      </td>
                  </tr>
              </tbody>
          </table>
      </td>
  </tr>
</tbody>
</table></td>
                                                                      </tr>
                                                                  </table>
                                                                  <!-- // END HEADER -->
                                                              </td>
                                                          </tr>
                                                      </table>
                                                  </td>
                                              </tr>
                                          </table>
                </td>
              </tr>
            </table>
            <!-- // END HEADER -->
          </td>
        </tr>
                      <tr>
                          <td align="center" valign="top">
                              <!-- BEGIN BODY // -->
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
                                  <tr>
                                      <td align="center" valign="top">
                                          <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                              <tr>
                                                  <td valign="top" class="bodyContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
<tbody class="mcnTextBlockOuter">
  <tr>
      <td valign="top" class="mcnTextBlockInner">

          <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
              <tbody><tr>

                  <td valign="top" class="mcnTextContent" style="padding: 9px 18px; font-family: 'Lucida Sans Unicode', 'Lucida Grande', sans-serif; font-size: 13px; line-height: 125%; text-align: justify;">

                      If above button is not working please copy paster the below link in your browser.<br>
                      <!--<?php //echo $email_verification_link ?><br> -->
<br>
Thanks for your help!<br>
&nbsp;
                  </td>
              </tr>
          </tbody></table>

      </td>
  </tr>
</tbody>
</table></td>
                                              </tr>
                                          </table>
                                      </td>
                                  </tr>
                              </table>
                              <!-- // END BODY -->
                          </td>
                      </tr>
                      <tr>
                          <td align="center" valign="top" style="padding-bottom:40px;">
                              <!-- BEGIN FOOTER // -->
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter">
                                  <tr>
                                      <td align="center" valign="top">
                                          <table border="0" cellpadding="0" cellspacing="0" width="600" class="templateContainer">
                                              <tr>
                                                  <td valign="top" class="footerContainer" style="padding-top:10px; padding-bottom:10px;"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock">
<tbody class="mcnDividerBlockOuter">
  <tr>
      <td class="mcnDividerBlockInner" style="padding: 18px;">
          <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top-width: 1px;border-top-style: solid;border-top-color: #AAAAAA;">
              <tbody><tr>
                  <td>
                      <span></span>
                  </td>
              </tr>
          </tbody></table>
      </td>
  </tr>
</tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
<tbody class="mcnTextBlockOuter">
  <tr>
      <td valign="top" class="mcnTextBlockInner">

          <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
              <tbody><tr>

                  <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">

                      <em>Copyright © 2015 Signzy, All rights reserved.</em><br>
<br>
<br>
<strong>Our mailing address is:</strong><br>
Cercles ,Hauz Khas VIllage ,New Delhi , 11017<br>
<br>
<a class="utilityLink" href="*|UNSUB|*">unsubscribe from this list</a>&nbsp;&nbsp;&nbsp; <a class="utilityLink" href="*|UPDATE_PROFILE|*">update subscription preferences</a>&nbsp;<br>
&nbsp;
                  </td>
              </tr>
          </tbody></table>

      </td>
  </tr>
</tbody>
</table></td>
                                              </tr>
                                          </table>
                                      </td>
                                  </tr>
                              </table>
                              <!-- // END FOOTER -->
                          </td>
                      </tr>
      </table>
      <!-- // END TEMPLATE -->
              </td>
          </tr>
      </table>
  </center>
