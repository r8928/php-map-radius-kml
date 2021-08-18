<?php
class KmlTemplate
{
    public $raduis = [];
    public $marker = [];
    public $state_name = '';

    public function __construct(string $state_name)
    {
        $this->state_name = strtoupper($state_name);
    }


    private function head()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
<Document>
<name>' . $this->state_name . '</name>
<description/>
<Style id="icon-normal"><IconStyle><color>ffd18802</color><scale>1.1</scale><Icon><href>https://www.gstatic.com/mapspro/images/stock/503-wht-blank_maps.png</href></Icon><hotSpot x="16" xunits="pixels" y="32" yunits="insetPixels"/></IconStyle><LabelStyle><scale>0</scale></LabelStyle></Style>
<Style id="icon-highlight"><IconStyle><color>ffd18802</color><scale>1.1</scale><Icon><href>https://www.gstatic.com/mapspro/images/stock/503-wht-blank_maps.png</href></Icon><hotSpot x="16" xunits="pixels" y="32" yunits="insetPixels"/></IconStyle><LabelStyle><scale>1.1</scale></LabelStyle></Style>
<Style id="poly-normal"><LineStyle><color>ff9b5701</color><width>2</width></LineStyle><PolyStyle><color>1c9b5701</color><fill>1</fill><outline>1</outline></PolyStyle></Style>
<Style id="poly-highlight"><LineStyle><color>ff9b5701</color><width>3</width></LineStyle><PolyStyle><color>1c9b5701</color><fill>1</fill><outline>1</outline></PolyStyle></Style>
<StyleMap id="poly"><Pair><key>normal</key><styleUrl>#poly-normal</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#poly-highlight</styleUrl></Pair><Pair><key>normal</key><styleUrl>#icon-normal</styleUrl></Pair><Pair><key>highlight</key><styleUrl>#icon-highlight</styleUrl></Pair></StyleMap>';
    }


    private function foot()
    {
        return '</Document>
</kml>';
    }


    public function addPlacemark(
        string $name,
        string $radius,
        array $extended_data,
        array $marker
    ) {
        $this->raduis[] = '

<Placemark>
<name>'
            . preg_replace('/\W+/', ' ', $name)
            . '</name>
<description/>
<styleUrl>#poly</styleUrl>
<Polygon>
<outerBoundaryIs>
<LinearRing>
<tessellate>1</tessellate>
<coordinates>' . $radius . '</coordinates>
</LinearRing>
</outerBoundaryIs>
</Polygon>
</Placemark>';

        $this->marker[] = '

<Placemark>
<name>' . preg_replace('/\W+/', ' ', $name) . '</name>
<description></description>
<styleUrl>#icon</styleUrl>
<Point><coordinates>' . implode(',', $marker) . ',0</coordinates></Point>
<ExtendedData>'
            . '<Data name="Name"><value><![CDATA[' .  $name . ']]></value></Data>'
            . $this->addExtendedData($extended_data)
            . '</ExtendedData>
</Placemark>';
    }


    public function addExtendedData($extended_data)
    {
        $xml = '';

        foreach ($extended_data as $key => $value) {
            $xml .= '<Data name="' . $key . '"><value><![CDATA[' .  $value . ']]></value></Data>';
        }

        return $xml;
    }


    public function get()
    {
        return
            $this->head()
            . '<Folder><name>' . $this->state_name . ' Markers</name>' . implode('', $this->marker) . '</Folder>'
            . '<Folder><name>' . $this->state_name . ' Radius</name>' . implode('', $this->raduis) . '</Folder>'
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
