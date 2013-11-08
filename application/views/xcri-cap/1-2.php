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
  <?php if (!empty($globals['ug']->contributor)): ?>
    <dc:contributor><![CDATA[<?php echo (strip_tags($globals['ug']->contributor)); ?>]]></dc:contributor>
  <?php endif; ?>
  <?php if (!empty($globals['ug']->catalog_description)): ?>
    <dc:description>
      <div xmlns="http://www.w3.org/1999/xhtml">
        <![CDATA[<?php echo ($globals['ug']->catalog_description); ?>]]>
      </div>
    </dc:description>
  <?php endif; ?>
    <provider>
      <?php if (!empty($globals['ug']->provider_description)): ?>
        <dc:description>
          <div xmlns="http://www.w3.org/1999/xhtml">
            <![CDATA[<?php echo ($globals['ug']->provider_description); ?>]]>
          </div>
        </dc:description>
      <?php endif; ?>
      <dc:identifier><?php echo ($globals['ug']->provider_url); ?></dc:identifier>
      <dc:identifier xsi:type="courseDataProgramme:ukprn"><?php echo ($globals['ug']->ukprn); ?></dc:identifier>
      <?php if (!empty($globals['ug']->image_source)): ?>
      <image src="<?php echo ($globals['ug']->image_source); ?>" title="<?php echo ($globals['ug']->image_title) ?>" alt="<?php echo ($globals['ug']->image_alt) ?>"/>
      <?php endif; ?>
      <dc:title><?php echo ($globals['ug']->institution_name); ?></dc:title>
      <mlo:url><?php echo ($globals['ug']->provider_url); ?></mlo:url>
      <?php echo View::make('xcri-cap.partials.ug-courses', array('programmes' => $programmes['ug'], 'globalsettings' => $globals['ug'])); ?>
      <?php echo View::make('xcri-cap.partials.pg-courses', array('programmes' => $programmes['pg'], 'globalsettings' => $globals['pg'])); ?>
      <mlo:location>
        <?php if (!empty($globals['ug']->town)): ?>
          <mlo:town><?php echo ($globals['ug']->town); ?></mlo:town>
        <?php endif; ?>
        <?php if (!empty($globals['ug']->postcode)): ?>
          <mlo:postcode><?php echo ($globals['ug']->postcode); ?></mlo:postcode>
        <?php endif; ?>
        <mlo:address><?php echo ($globals['ug']->address_line_1); ?></mlo:address>
        <?php if (!empty($globals['ug']->phone)): ?>
          <mlo:phone><?php echo ($globals['ug']->phone); ?></mlo:phone>
        <?php endif; ?>
        <?php if (!empty($globals['ug']->fax)): ?>
          <mlo:fax><?php echo ($globals['ug']->fax); ?></mlo:fax>
        <?php endif; ?>
        <?php if (!empty($globals['ug']->email)): ?><mlo:email><?php echo ($globals['ug']->email); ?></mlo:email><?php endif; ?><?php if ($globals['ug']->provider_url): ?><mlo:url><?php echo ($globals['ug']->provider_url); ?></mlo:url><?php endif; ?>
      </mlo:location>
    </provider>
</catalog>