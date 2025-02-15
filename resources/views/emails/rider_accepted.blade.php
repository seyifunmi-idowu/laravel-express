<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fele Express</title>

    <style>
      @font-face {
        font-family: Helvetica Neue, Roboto, sans-serif;
      }

      .bodyWrapper {
        font-family: Helvetica Neue, Roboto, sans-serif;
        margin: 0;
      }

      table {
        border-spacing: 0;
      }

      td {
        padding: 0;
      }

      img {
        border: 0;
      }
      li {
        color: #4d4d4d;
        font-size: 14px;
        margin-bottom: 5px;
        line-height: 24px;
      }

      p {
        font-size: 14px;
        line-height: 26px;
        color: #4d4d4d;
      }

      .wrapper {
        background-color: #ffffff;
        max-width: 698px;
        margin: 0 auto;
        table-layout: fixed;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
      }

      .main {
        width: 100%;
        background: transparent;
      }

      .header {
        background: #f9fcff;
      }

      .logo {
        width: 98px;
        padding: 20px 50px;
      }

      .content {
        padding: 40px 57px;
      }

      .intro-text {
        font-size: 26px;
        font-weight: 600;
        margin-bottom: 18px;
      }
      .sub-header {
        font-size: 20px;
        font-weight: 600;
      }

      .btnStyle {
        width: 138px;
        height: 53px;
        background: #EA661A;
        border-radius: 30px;
        font-size: 14px;
        text-align: center;
        color: #ffffff !important;
        font-weight: bold;
        border: 0rem;
        cursor: pointer;
      }

      .footer {
        background-color: #EA661A;
        color: white;
        padding: 30px 54px;
        font-size: 14px;
      }

      .footer__table {
        width: 100%;
        border-bottom: 0.3px solid #f4f4f4;
        padding-bottom: 15px;
      }

      .footer__table tr {
        display: inline-block;
      }

      .footer__nav {
        color: #f9fcff;
        text-decoration: none;
        display: block;
        line-height: 26px;
        text-align: end;
      }

      .footer__address,
      .footer__copy {
        color: #f9fcff;
        font-size: 12px;
        display: inline-block;
      }

      .footer__copy {
        float: right;
      }

      @media only screen and (max-width: 600px) {
        .bodyWrapper {
          background-color: #ffffff !important;
        }

        .wrapper {
          margin: 0;
        }

        .logo {
          padding: 10px 12px !important;
        }

        .content {
          padding: 48px 20px 80px 20px !important;
        }

        .intro-text {
          font-size: 22px !important;
          margin-bottom: 18px;
        }

        .btnStyle {
          width: 104px;
          height: 47px;
        }

        .footer {
          padding: 44px 20px 34px 20px !important;
          overflow: hidden;
        }

        .hero-image {
          width: 100%;
          text-align: center;
          margin-bottom: 20px;
        }
      }

      @media only screen and (max-width: 550px) {
        .footer__address {
          display: block !important;
        }

        .footer__copy {
          float: none !important;
          margin-top: 0px !important;
        }
      }

      @media only screen and (max-width: 450px) {
        .intro-text {
          font-size: 20px !important;
        }

        .footer__nav {
          font-size: 12px !important;
        }
      }

      @media only screen and (min-width: 601px) {
        .bodyWrapper {
          background-color: #f5f5f5 !important;
        }
      }
    </style>
  </head>
  <body class="bodyWrapper">
    <div class="wrapper">
      <table class="main" style="width: 100%">
        <tr>
            <td class="header">
                <img
                  class="logo"
                  src="https://feleexpress.s3.amazonaws.com/emails/fele-logo.png"
                  alt="logo image"
                  style="width: 300px;"
                />
              </td>
        </tr>
        <tr>
          <td class="content">
            <table>
              <tr>
                <td>
                  <h1 class="intro-text">Your Documents Have Been Accepted</h1>
                  <p
                    style="
                      font-size: 14px;
                      line-height: 26px;
                      margin: 0 0 18px 0;
                      color: #4d4d4d;
                    "
                  >
                      Hi {{ $display_name }},
                      <br/>
                      <br/>
                      We’re thrilled to inform you that your verification request to be a Fele Partner driver has been approved. You are now live on Fele Express.
                  </p>
                    <h2 class="sub-header">What next?</h2>
                  <p>
                      Now that you have been approved, you may proceed with driver training in the office. Visit our website to choose an orientation day: <a target="_blank" href="https://feleexpress.com/choose-date/">Choose date here</a>
                  </p>
                  <p>
                    Once your date has been confirmed, you may visit our office at <strong>No 10 Adical plaza, 70 Old Otukpo Road, Makurdi</strong> to undergo your orientation and training.
                  </p>
                  <p>
                    Once you are done with orientation, you may start earning by accepting orders.
                  </p>
                    <h2 class="sub-header">Need help?</h2>
                  <p
                    style="
                      font-size: 14px;
                      line-height: 26px;
                      margin: 0 0 18px 0;
                      color: #4d4d4d;
                    "
                  >
                      Our friendly support team is only a quick email or WhatsApp message away! You can contact us by replying to this email or on WhatsApp <strong>+2349112529296</strong>.
                  </p>
                <p>

                    Best regards, <br/>
                    Fele Team
                  </p>

                </td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td class="footer" style="height: 267px;">
            <table>
              <tr>
                <td>
                  <table class="footer__table">
                    <tr style="margin-right: auto">
                      <td style="display: block; margin-bottom: 20px">
                        <img
                          style="width: 62px"
                          src="https://feleexpress.s3.amazonaws.com/emails/fele-logo.png"
                          alt="logo image"
                        />
                      </td>

                      <td class="icons">
                           <p class="footer__address">
                             +2349112529296
                             <br/>
                             hello@feleexpress.com
                             <br/>
                             Makurdi Office:
                             <br/>
                             No 10 Adical plaza, 70 Old Otukpo Road, Makurdi Benue State, Nigeria
                          </p>
                           <p class="footer__address">

                          </p>

                      </td>
                    </tr>

                    <tr style="float: right; padding-bottom: 22px">
                      <td>
                        <a
                          href="https://feleexpress.com/terms-conditions/"
                          class="footer__nav"
                          style="color: white !important;"
                          >Terms and Condition</a
                        >
                        <a
                          href="https://feleexpress.com/privacy-policy"
                          class="footer__nav"
                          style="color: white !important;"
                          >Privacy Policy</a>
                        <a
                          href="mailto:hello@feleexpress.com"
                          class="footer__nav"
                          style="color: white !important;"
                          >Contact Support</a
                        >
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td>
                  <p>
                    <span
                      style="
                        font-size: 10px;
                        line-height: 14px;
                        font-weight: 400px;
                        size: 10px;
                        color: white !important;
                      "
                      >
                      You are receiving this email because you signed up with your
                      email address on Fele. If you think this is a mistake,
                      please reach out to our support team. See our Terms of use &
                      Privacy policy for more information.
                    </span>
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
