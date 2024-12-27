<style>
  #flash {
    & ul {
      list-style: none;

      li {
        width: max-content;
        padding: 0.5em;
        background-color: lightgray;
      }
    }
  }

  #fulfillment-services {
    padding: 2em;

    display: grid;
    grid-template-columns: 0.5fr 1fr;
    align-items: start;
    gap: 1em;
  }

  #form {
    display: flex;
    flex-flow: row wrap;
    gap: 0.5em;

    &>* {
      flex: 1 1 0;
    }
  }

  #table {
    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: 1fr;

    overflow-x: auto;

    & .row {
      display: grid;
      grid-template-columns: 0.1fr 0.25fr 0.25fr 0.25fr 0.25fr;
      gap: 1em;
      padding: 0.5em;

      &:nth-child(odd) {
        background-color: lightgray;
      }
    }

    & .row.heading {
      background-color: gray;
      color: white;
    }

    & .column {
      word-break: break-all;
    }
  }

  #note {
    margin-block-start: 2em;

    & p {
      font-size: 0.75rem;
      color: red;
    }
  }
</style>


<?php
$errors = $this->flash('errors');
$notices = $this->flash('notices');
?>
<?php if (!empty($errors) || !empty($notices)): ?>
  <div id="flash">
    <?php if (!empty($errors)): ?>
      <ul class="errors">
        <?php foreach ($errors as $error): ?>
          <li onclick="this.style.display='none';"><span>&#10006;</span> <span>&nbsp;&#9474;&nbsp;</span> <span><?= $error; ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <?php if ($notices): ?>
      <ul class="notices">
        <?php foreach ($notices as $notice): ?>
          <li onclick="this.style.display='none';"><span>&#10004;</span> <span>&nbsp;&#9474;&nbsp;</span> <span><?= $notice; ?></span></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div id="fulfillment-services">
  <div>

    <form id="form" action="<?= $this->path("/fulfillment-services"); ?>" method="post">
      <input type="text" name="name" placeholder="Fulfillment Service Name" required>
      <input type="text" name="address1" placeholder="Address Line 1">
      <input type="text" name="address2" placeholder="Address Line 2">
      <input type="text" name="city" placeholder="City">
      <input type="text" name="zip" placeholder="Zip Code">
      <select name="country" id="country">
        <option value="AC">Ascension Island</option>
        <option value="AD">Andorra</option>
        <option value="AE">United Arab Emirates</option>
        <option value="AF">Afghanistan</option>
        <option value="AG">Antigua & Barbuda</option>
        <option value="AI">Anguilla</option>
        <option value="AL">Albania</option>
        <option value="AM">Armenia</option>
        <option value="AN">Netherlands Antilles</option>
        <option value="AO">Angola</option>
        <option value="AR">Argentina</option>
        <option value="AT">Austria</option>
        <option value="AU">Australia</option>
        <option value="AW">Aruba</option>
        <option value="AX">Åland Islands</option>
        <option value="AZ">Azerbaijan</option>
        <option value="BA">Bosnia & Herzegovina</option>
        <option value="BB">Barbados</option>
        <option value="BD">Bangladesh</option>
        <option value="BE">Belgium</option>
        <option value="BF">Burkina Faso</option>
        <option value="BG">Bulgaria</option>
        <option value="BH">Bahrain</option>
        <option value="BI">Burundi</option>
        <option value="BJ">Benin</option>
        <option value="BL">St. Barthélemy</option>
        <option value="BM">Bermuda</option>
        <option value="BN">Brunei</option>
        <option value="BO">Bolivia</option>
        <option value="BQ">Caribbean Netherlands</option>
        <option value="BR">Brazil</option>
        <option value="BS">Bahamas</option>
        <option value="BT">Bhutan</option>
        <option value="BV">Bouvet Island</option>
        <option value="BW">Botswana</option>
        <option value="BY">Belarus</option>
        <option value="BZ">Belize</option>
        <option value="CA">Canada</option>
        <option value="CC">Cocos (Keeling) Islands</option>
        <option value="CD">Congo - Kinshasa</option>
        <option value="CF">Central African Republic</option>
        <option value="CG">Congo - Brazzaville</option>
        <option value="CH">Switzerland</option>
        <option value="CI">Côte d'Ivoire</option>
        <option value="CK">Cook Islands</option>
        <option value="CL">Chile</option>
        <option value="CM">Cameroon</option>
        <option value="CN">China</option>
        <option value="CO">Colombia</option>
        <option value="CR">Costa Rica</option>
        <option value="CU">Cuba</option>
        <option value="CV">Cape Verde</option>
        <option value="CW">Curaçao</option>
        <option value="CX">Christmas Island</option>
        <option value="CY">Cyprus</option>
        <option value="CZ">Czechia</option>
        <option value="DE">Germany</option>
        <option value="DJ">Djibouti</option>
        <option value="DK">Denmark</option>
        <option value="DM">Dominica</option>
        <option value="DO">Dominican Republic</option>
        <option value="DZ">Algeria</option>
        <option value="EC">Ecuador</option>
        <option value="EE">Estonia</option>
        <option value="EG">Egypt</option>
        <option value="EH">Western Sahara</option>
        <option value="ER">Eritrea</option>
        <option value="ES">Spain</option>
        <option value="ET">Ethiopia</option>
        <option value="FI">Finland</option>
        <option value="FJ">Fiji</option>
        <option value="FK">Falkland Islands</option>
        <option value="FO">Faroe Islands</option>
        <option value="FR">France</option>
        <option value="GA">Gabon</option>
        <option value="GB">United Kingdom</option>
        <option value="GD">Grenada</option>
        <option value="GE">Georgia</option>
        <option value="GF">French Guiana</option>
        <option value="GG">Guernsey</option>
        <option value="GH">Ghana</option>
        <option value="GI">Gibraltar</option>
        <option value="GL">Greenland</option>
        <option value="GM">Gambia</option>
        <option value="GN">Guinea</option>
        <option value="GP">Guadeloupe</option>
        <option value="GQ">Equatorial Guinea</option>
        <option value="GR">Greece</option>
        <option value="GS">South Georgia & South Sandwich Islands</option>
        <option value="GT">Guatemala</option>
        <option value="GW">Guinea-Bissau</option>
        <option value="GY">Guyana</option>
        <option value="HK">Hong Kong SAR</option>
        <option value="HM">Heard & McDonald Islands</option>
        <option value="HN">Honduras</option>
        <option value="HR">Croatia</option>
        <option value="HT">Haiti</option>
        <option value="HU">Hungary</option>
        <option value="ID">Indonesia</option>
        <option value="IE">Ireland</option>
        <option value="IL">Israel</option>
        <option value="IM">Isle of Man</option>
        <option value="IN">India</option>
        <option value="IO">British Indian Ocean Territory</option>
        <option value="IQ">Iraq</option>
        <option value="IR">Iran</option>
        <option value="IS">Iceland</option>
        <option value="IT">Italy</option>
        <option value="JE">Jersey</option>
        <option value="JM">Jamaica</option>
        <option value="JO">Jordan</option>
        <option value="JP">Japan</option>
        <option value="KE">Kenya</option>
        <option value="KG">Kyrgyzstan</option>
        <option value="KH">Cambodia</option>
        <option value="KI">Kiribati</option>
        <option value="KM">Comoros</option>
        <option value="KN">St. Kitts & Nevis</option>
        <option value="KP">North Korea</option>
        <option value="KR">South Korea</option>
        <option value="KW">Kuwait</option>
        <option value="KY">Cayman Islands</option>
        <option value="KZ">Kazakhstan</option>
        <option value="LA">Laos</option>
        <option value="LB">Lebanon</option>
        <option value="LC">St. Lucia</option>
        <option value="LI">Liechtenstein</option>
        <option value="LK">Sri Lanka</option>
        <option value="LR">Liberia</option>
        <option value="LS">Lesotho</option>
        <option value="LT">Lithuania</option>
        <option value="LU">Luxembourg</option>
        <option value="LV">Latvia</option>
        <option value="LY">Libya</option>
        <option value="MA">Morocco</option>
        <option value="MC">Monaco</option>
        <option value="MD">Moldova</option>
        <option value="ME">Montenegro</option>
        <option value="MF">St. Martin</option>
        <option value="MG">Madagascar</option>
        <option value="MK">North Macedonia</option>
        <option value="ML">Mali</option>
        <option value="MM">Myanmar (Burma)</option>
        <option value="MN">Mongolia</option>
        <option value="MO">Macao SAR</option>
        <option value="MQ">Martinique</option>
        <option value="MR">Mauritania</option>
        <option value="MS">Montserrat</option>
        <option value="MT">Malta</option>
        <option value="MU">Mauritius</option>
        <option value="MV">Maldives</option>
        <option value="MW">Malawi</option>
        <option value="MX">Mexico</option>
        <option value="MY">Malaysia</option>
        <option value="MZ">Mozambique</option>
        <option value="NA">Namibia</option>
        <option value="NC">New Caledonia</option>
        <option value="NE">Niger</option>
        <option value="NF">Norfolk Island</option>
        <option value="NG">Nigeria</option>
        <option value="NI">Nicaragua</option>
        <option value="NL">Netherlands</option>
        <option value="NO">Norway</option>
        <option value="NP">Nepal</option>
        <option value="NR">Nauru</option>
        <option value="NU">Niue</option>
        <option value="NZ">New Zealand</option>
        <option value="OM">Oman</option>
        <option value="PA">Panama</option>
        <option value="PE">Peru</option>
        <option value="PF">French Polynesia</option>
        <option value="PG">Papua New Guinea</option>
        <option value="PH">Philippines</option>
        <option value="PK">Pakistan</option>
        <option value="PL">Poland</option>
        <option value="PM">St. Pierre & Miquelon</option>
        <option value="PN">Pitcairn Islands</option>
        <option value="PS">Palestinian Territories</option>
        <option value="PT">Portugal</option>
        <option value="PY">Paraguay</option>
        <option value="QA">Qatar</option>
        <option value="RE">Réunion</option>
        <option value="RO">Romania</option>
        <option value="RS">Serbia</option>
        <option value="RU">Russia</option>
        <option value="RW">Rwanda</option>
        <option value="SA">Saudi Arabia</option>
        <option value="SB">Solomon Islands</option>
        <option value="SC">Seychelles</option>
        <option value="SD">Sudan</option>
        <option value="SE">Sweden</option>
        <option value="SG">Singapore</option>
        <option value="SH">St. Helena</option>
        <option value="SI">Slovenia</option>
        <option value="SJ">Svalbard & Jan Mayen</option>
        <option value="SK">Slovakia</option>
        <option value="SL">Sierra Leone</option>
        <option value="SM">San Marino</option>
        <option value="SN">Senegal</option>
        <option value="SO">Somalia</option>
        <option value="SR">Suriname</option>
        <option value="SS">South Sudan</option>
        <option value="ST">São Tomé & Príncipe</option>
        <option value="SV">El Salvador</option>
        <option value="SX">Sint Maarten</option>
        <option value="SY">Syria</option>
        <option value="SZ">Eswatini</option>
        <option value="TA">Tristan da Cunha</option>
        <option value="TC">Turks & Caicos Islands</option>
        <option value="TD">Chad</option>
        <option value="TF">French Southern Territories</option>
        <option value="TG">Togo</option>
        <option value="TH">Thailand</option>
        <option value="TJ">Tajikistan</option>
        <option value="TK">Tokelau</option>
        <option value="TL">Timor-Leste</option>
        <option value="TM">Turkmenistan</option>
        <option value="TN">Tunisia</option>
        <option value="TO">Tonga</option>
        <option value="TR">Türkiye</option>
        <option value="TT">Trinidad & Tobago</option>
        <option value="TV">Tuvalu</option>
        <option value="TW">Taiwan</option>
        <option value="TZ">Tanzania</option>
        <option value="UA">Ukraine</option>
        <option value="UG">Uganda</option>
        <option value="UM">U.S. Outlying Islands</option>
        <option value="US" selected>United States</option>
        <option value="UY">Uruguay</option>
        <option value="UZ">Uzbekistan</option>
        <option value="VA">Vatican City</option>
        <option value="VC">St. Vincent & Grenadines</option>
        <option value="VE">Venezuela</option>
        <option value="VG">British Virgin Islands</option>
        <option value="VN">Vietnam</option>
        <option value="VU">Vanuatu</option>
        <option value="WF">Wallis & Futuna</option>
        <option value="WS">Samoa</option>
        <option value="XK">Kosovo</option>
        <option value="YE">Yemen</option>
        <option value="YT">Mayotte</option>
        <option value="ZA">South Africa</option>
        <option value="ZM">Zambia</option>
        <option value="ZW">Zimbabwe</option>
        <option value="ZZ">Unknown Region</option>
      </select>
      <input type="text" name="phone" placeholder="Phone">
      <input type="text" name="api" placeholder="API Key" required>
      <button type="submit">Create</button>
    </form>

    <div id="note">
      <p>* Please generate an API KEY by creating a private app in shopify admin dashboard.</p>
      <p>* A generated fulfillment service can only be queried, updated, edited, or removed by the API key that created it.</p>
    </div>
  </div>
  <div id="table">
    <div class="row heading">
      <div class="column">ID</div>
      <div class="column">API</div>
      <div class="column">NAME</div>
      <div class="column">FULFILLMENT SERVICE ID</div>
      <div class="column">LOCATION ID</div>
    </div>
    <?php if (!empty($services)): ?>
      <?php foreach ($services as $service): ?>
        <div class="row">
          <div class="column"><?= $service['id']; ?></div>
          <div class="column"><?= $service['api_key']; ?></div>
          <div class="column"><?= $service['name']; ?></div>
          <div class="column"><?= $service['fulfillment_service_id']; ?></div>
          <div class="column"><?= $service['location_id']; ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>