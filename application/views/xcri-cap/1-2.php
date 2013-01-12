<?php echo "<?xml"; ?> version="1.0" encoding="UTF-8"<?php echo "?>\n"; ?>
<catalog
  xmlns="http://xcri.org/profiles/1.2/catalog"
  xmlns:xcriTerms="http://xcri.org/profiles/catalog/terms"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xmlns:xhtml="http://www.w3.org/1999/xhtml"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xmlns:credit="http://purl.org/net/cm"
  xmlns:mlo="http://purl.org/net/mlo"
  xmlns:courseDataProgramme="http://xcri.co.uk"
  xsi:schemaLocation="http://xcri.org/profiles/1.2/catalog http://www.xcri.co.uk/bindings/xcri_cap_1_2.xsd http://xcri.org/profiles/1.2/catalog/terms  http://www.xcri.co.uk/bindings/xcri_cap_terms_1_2.xsd http://xcri.co.uk http://www.xcri.co.uk/bindings/coursedataprogramme.xsd"
  generated="<?php echo "2012-04-11T17:36:22.218Z"; ?>">
  <?php if ($globalsettings->contributor_3): ?>
    <dc:contributor><?php echo $globalsettings->contributor_3; ?></dc:contributor>
  <?php endif; ?>
  <?php if ($globalsettings->catalog_description_4): ?>
    <dc:description>
      <div xmlns="http://www.w3.org/1999/xhtml">
        <?php echo $globalsettings->catalog_description_4; ?>
      </div>
    </dc:description>
  <?php endif; ?>
    <provider>
      <?php if ($globalsettings->provider_description_5): ?>
        <dc:description>
          <div xmlns="http://www.w3.org/1999/xhtml">
            <?php echo $globalsettings->provider_description_5; ?>
          </div>
        </dc:description>
      <?php endif; ?>
      <dc:identifier><?php echo $globalsettings->provider_url_6; ?></dc:identifier>
      <dc:identifier xsi:type="courseDataProgramme:ukprn"><?php echo $globalsettings->ukprn_2; ?></dc:identifier>
      <dc:title><?php echo $globalsettings->institution_name_1; ?></dc:title>
      <mlo:url><?php echo $globalsettings->provider_url_6; ?></mlo:url>
      <mlo:location>
        <?php if ($globalsettings->town_10): ?>
          <mlo:town><?php echo $globalsettings->town_10; ?></mlo:town>
        <?php endif; ?>
        <?php if ($globalsettings->postcode_14): ?>
          <mlo:postcode><?php echo $globalsettings->postcode_14; ?></mlo:postcode>
        <?php endif; ?>
        <mlo:address><?php echo $globalsettings->address_line_1_7; ?></mlo:address>
        <?php if ($globalsettings->phone_13): ?>
          <mlo:phone><?php echo $globalsettings->phone_13; ?></mlo:phone>
        <?php endif; ?>
        <?php if ($globalsettings->fax_12): ?>
          <mlo:fax><?php echo $globalsettings->fax_12; ?></mlo:fax>
        <?php endif; ?>
        <?php if ($globalsettings->email_11): ?>
          <mlo:email><?php echo $globalsettings->email_11; ?></mlo:email>
        <?php endif; ?>
        <?php if ($globalsettings->provider_url_6): ?>
          <mlo:url><?php echo $globalsettings->provider_url_6; ?></mlo:url>
        <?php endif; ?>
      </mlo:location>
    </provider>
</catalog>