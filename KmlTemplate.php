<?php
class KmlTemplate
{
    public $raduis = [];
    public $marker = [];
    public $state_name = '';
    private $style_rnd = '';
    private $marker_color = '1c9b5701';
    private $radius_line_color = 'ff9b5701';
    private $radius_poly_color = '1c9b5701';
    private $include_markers = true;
    private $include_radius = true;

    public function __construct(string $state_name)
    {
        $this->state_name = strtoupper($state_name);
        $this->style_rnd = 'style-' . md5(time()) . '-';
    }


    private function head()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
<name>' . $this->state_name . '</name>
<description/>
<Style id="' . $this->style_rnd . 'icon-normal"><IconStyle><color>' . $this->marker_color . '</color><scale>1.1</scale><Icon><href>https://www.gstatic.com/mapspro/images/stock/503-wht-blank_maps.png</href></Icon><hotSpot x="16" xunits="pixels" y="32" yunits="insetPixels"/></IconStyle><LabelStyle><scale>0</scale></LabelStyle></Style>
<Style id="' . $this->style_rnd . 'icon-highlight"><IconStyle><color>' . $this->marker_color . '</color><scale>1.1</scale><Icon><href>https://www.gstatic.com/mapspro/images/stock/503-wht-blank_maps.png</href></Icon><hotSpot x="16" xunits="pixels" y="32" yunits="insetPixels"/></IconStyle><LabelStyle><scale>1.1</scale></LabelStyle></Style>
<Style id="' . $this->style_rnd . 'poly-normal"><LineStyle><color>' . $this->radius_line_color . '</color><width>2</width></LineStyle><PolyStyle><color>' . $this->radius_poly_color . '</color><fill>1</fill><outline>1</outline></PolyStyle></Style>
<Style id="' . $this->style_rnd . 'poly-highlight"><LineStyle><color>' . $this->radius_line_color . '</color><width>3</width></LineStyle><PolyStyle><color>' . $this->radius_poly_color . '</color><fill>1</fill><outline>1</outline></PolyStyle></Style>
<StyleMap id="' . $this->style_rnd . 'poly"><Pair><key>normal</key><styleUrl>#' . $this->style_rnd . 'poly-normal</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#' . $this->style_rnd . 'poly-highlight</styleUrl></Pair><Pair><key>normal</key><styleUrl>#' . $this->style_rnd . 'icon-normal</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#' . $this->style_rnd . 'icon-highlight</styleUrl></Pair></StyleMap>

';
    }


    private function foot()
    {
        return '</Document>
</kml>';
    }


    public function dontIncludeRadius()
    {
        $this->include_radius = false;

        return $this;
    }


    public function includeRadius()
    {
        $this->include_radius = true;

        return $this;
    }


    public function radiusColor(string $line_color, string $poly_color)
    {
        $this->radius_line_color = $line_color;
        $this->radius_poly_color = $poly_color;

        return $this;
    }


    public function dontIncludeMarkers()
    {
        $this->include_markers = false;

        return $this;
    }


    public function includeMarkers()
    {
        $this->include_markers = true;

        return $this;
    }


    public function markerColor(string $marker_color)
    {
        $this->marker_color = $marker_color;

        return $this;
    }


    public function addPlacemarks(
        string $name,
        string $radius_coordinates,
        array $extended_data,
        array $marker_coordinates
    ) {
        $this->addRadius($name, $radius_coordinates);
        $this->addMarker($name, $marker_coordinates, $extended_data);
    }


    private function addRadius(string $name, string $coordinates, array $extended_data = [])
    {
        $this->raduis[] = '

<Placemark>
<name>'
            . preg_replace('/\W+/', ' ', $name)
            . '</name>
<description/>
<styleUrl>#' . $this->style_rnd . 'poly</styleUrl>
<Polygon>
<outerBoundaryIs>
<LinearRing>
<tessellate>1</tessellate>
<coordinates>' . $coordinates . '</coordinates>
</LinearRing>
</outerBoundaryIs>
</Polygon>'
            . $this->addExtendedData($name, $extended_data) .
            '</Placemark>';
    }


    private function addMarker(string $name, array $coordinates, array $extended_data)
    {
        $this->marker[] = '

<Placemark>
<name>' . preg_replace('/\W+/', ' ', $name) . '</name>
<description></description>
<styleUrl>#' . $this->style_rnd . 'icon</styleUrl>
<Point><coordinates>' . implode(',', $coordinates) . ',0</coordinates></Point>'
            . $this->addExtendedData($name, $extended_data) .
            '</Placemark>';
    }


    public function addExtendedData($name, $extended_data)
    {
        $xml = '';
        if (sizeof($extended_data) > 0) {
            $xml = '<ExtendedData>'
                . '<Data name="Name"><value><![CDATA[' .  $name . ']]></value></Data>';

            foreach ($extended_data as $key => $value) {
                $xml .= '<Data name="' . $key . '"><value><![CDATA[' .  $value . ']]></value></Data>';
            }

            $xml = $xml . '</ExtendedData>';
        }
        return $xml;
    }


    public function get()
    {
        return $this->head()

            . (($this->include_markers && sizeof($this->marker) > 0)
                ? '<Folder><name>' . $this->state_name . ' Markers</name>' . implode('', $this->marker) . '</Folder>'
                : '')

            . (($this->include_radius && sizeof($this->raduis) > 0)
                ? '<Folder><name>' . $this->state_name . ' 1MI Radius</name>' . implode('', $this->raduis) . '</Folder>'
                : '')

            . $this->foot()
            //
        ;
    }


    public function show()
    {
        echo $this->get();
    }


    public function save()
    {
        file_put_contents(
            'kml/' . $this->state_name . '.kml',
            $this->get()
        );
    }
}
