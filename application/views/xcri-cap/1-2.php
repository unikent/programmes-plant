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
  <?php if (!empty($globalsettings->contributor)): ?>
    <dc:contributor><![CDATA[<?php echo (strip_tags($globalsettings->contributor)); ?>]]></dc:contributor>
  <?php endif; ?>
  <?php if (!empty($globalsettings->catalog_description)): ?>
    <dc:description>
      <div xmlns="http://www.w3.org/1999/xhtml">
        <![CDATA[<?php echo ($globalsettings->catalog_description); ?>]]>
      </div>
    </dc:description>
  <?php endif; ?>
    <provider>
      <?php if (!empty($globalsettings->provider_description)): ?>
        <dc:description>
          <div xmlns="http://www.w3.org/1999/xhtml">
            <![CDATA[<?php echo ($globalsettings->provider_description); ?>]]>
          </div>
        </dc:description>
      <?php endif; ?>
      <dc:identifier><?php echo ($globalsettings->provider_url); ?></dc:identifier>
      <dc:identifier xsi:type="courseDataProgramme:ukprn"><?php echo ($globalsettings->ukprn); ?></dc:identifier>
      <?php if (!empty($globalsettings->image_source)): ?>
      <image src="<?php echo ($globalsettings->image_source); ?>" title="<?php echo ($globalsettings->image_title) ?>" alt="<?php echo ($globalsettings->image_alt) ?>"/>
      <?php endif; ?>
      <dc:title><?php echo ($globalsettings->institution_name); ?></dc:title>
      <mlo:url><?php echo ($globalsettings->provider_url); ?></mlo:url>
      <?php echo View::make('xcri-cap.partials.ug-courses', array('programmes' => $programmes['ug'], 'globalsettings' => $globalsettings)); ?>
      <?php echo View::make('xcri-cap.partials.pg-courses', array('programmes' => $programmes['pg'], 'globalsettings' => $globalsettings)); ?>
      <mlo:location>
        <?php if (!empty($globalsettings->town)): ?>
          <mlo:town><?php echo ($globalsettings->town); ?></mlo:town>
        <?php endif; ?>
        <?php if (!empty($globalsettings->postcode)): ?>
          <mlo:postcode><?php echo ($globalsettings->postcode); ?></mlo:postcode>
        <?php endif; ?>
        <mlo:address><?php echo ($globalsettings->address_line_1); ?></mlo:address>
        <?php if (!empty($globalsettings->phone)): ?>
          <mlo:phone><?php echo ($globalsettings->phone); ?></mlo:phone>
        <?php endif; ?>
        <?php if (!empty($globalsettings->fax)): ?>
          <mlo:fax><?php echo ($globalsettings->fax); ?></mlo:fax>
        <?php endif; ?>
        <?php if (!empty($globalsettings->email)): ?><mlo:email><?php echo ($globalsettings->email); ?></mlo:email><?php endif; ?><?php if ($globalsettings->provider_url): ?><mlo:url><?php echo ($globalsettings->provider_url); ?></mlo:url><?php endif; ?>
      </mlo:location>
    </provider>
</catalog>