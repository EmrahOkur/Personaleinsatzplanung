<?php

declare(strict_types=1);

namespace App\Services;

class AddressService
{
    protected $addresses;

    public function __construct()
    {
        $this->addresses = [
            [
                'street' => 'Spitzerdorfstrasse 1',
                'city' => 'Wedel',
                'zip_code' => '22880',
            ],
            [
                'street' => 'Domhof 37',
                'city' => 'Ratzeburg',
                'zip_code' => '23909',
            ],
            [
                'street' => 'Lubecker Strasse 7',
                'city' => 'Eutin',
                'zip_code' => '23701',
            ],
            [
                'street' => 'Aspelohe 27',
                'city' => 'Norderstedt',
                'zip_code' => '22848',
            ],
            [
                'street' => 'Tondernsche Strasse 4',
                'city' => 'Bredstedt',
                'zip_code' => '25821',
            ],
            [
                'street' => 'Zur Exe 23',
                'city' => 'Flensburg',
                'zip_code' => '24937',
            ],
            [
                'street' => 'Moltkestrasse 12',
                'city' => 'Pinneberg',
                'zip_code' => '25421',
            ],
            [
                'street' => 'Rathausmarkt 10',
                'city' => 'Schleswig',
                'zip_code' => '24837',
            ],
            [
                'street' => 'Bergstrasse 1',
                'city' => 'Bad Oldesloe',
                'zip_code' => '23843',
            ],
            [
                'street' => 'Moltkestrasse 12',
                'city' => 'Pinneberg',
                'zip_code' => '25421',
            ],
            [
                'street' => 'Fordepromenade 4',
                'city' => 'Flensburg',
                'zip_code' => '24944',
            ],
            [
                'street' => 'Ramskamp 13',
                'city' => 'Elmshorn',
                'zip_code' => '25337',
            ],
            [
                'street' => 'Kieler Strasse 1',
                'city' => 'Nortorf',
                'zip_code' => '24589',
            ],
            [
                'street' => 'Muhlenstrasse 21',
                'city' => 'Elmshorn',
                'zip_code' => '25335',
            ],
            [
                'street' => 'Robert-Bosch-Strasse 4-8',
                'city' => 'Quickborn',
                'zip_code' => '25451',
            ],
            [
                'street' => 'Mollner Landstrasse 31',
                'city' => 'Glinde',
                'zip_code' => '21509',
            ],
            [
                'street' => 'Ramskamp 13',
                'city' => 'Elmshorn',
                'zip_code' => '25337',
            ],
            [
                'street' => 'Rathausallee 64-66',
                'city' => 'Norderstedt',
                'zip_code' => '22846',
            ],
            [
                'street' => 'Norderstrasse 14-22',
                'city' => 'Geesthacht',
                'zip_code' => '21502',
            ],
            [
                'street' => 'Am Friedenshugel 3',
                'city' => 'Flensburg',
                'zip_code' => '24941',
            ],
            [
                'street' => 'Suderdamm 4',
                'city' => 'Heide',
                'zip_code' => '25746',
            ],
            [
                'street' => 'Am Bahnhof 9',
                'city' => 'Rendsburg',
                'zip_code' => '24768',
            ],
            [
                'street' => 'Paradeplatz 3',
                'city' => 'Rendsburg',
                'zip_code' => '24768',
            ],
            [
                'street' => 'Seestern-Pauly-Strasse 10',
                'city' => 'Schwarzenbek',
                'zip_code' => '21493',
            ],
            [
                'street' => 'Sudermarkt 1',
                'city' => 'Meldorf',
                'zip_code' => '25704',
            ],
            [
                'street' => 'Am Mittelburgwall 24',
                'city' => 'Friedrichstadt',
                'zip_code' => '25840',
            ],
            [
                'street' => 'Altonaer Chaussee 49',
                'city' => 'Schenefeld',
                'zip_code' => '22869',
            ],
            [
                'street' => 'Hamburger Strasse 205',
                'city' => 'Elmshorn',
                'zip_code' => '25337',
            ],
            [
                'street' => 'Osterblick 1',
                'city' => 'Meldorf',
                'zip_code' => '25704',
            ],
            [
                'street' => 'Rondeel 7',
                'city' => 'Ahrensburg',
                'zip_code' => '22926',
            ],
            [
                'street' => 'Reichenstrasse 6',
                'city' => 'Barmstedt',
                'zip_code' => '25355',
            ],
            [
                'street' => 'Sudstrandpromenade',
                'city' => 'Fehmarn',
                'zip_code' => '23769',
            ],
            [
                'street' => 'Willy-Meyer-Strasse 3',
                'city' => 'Tornesch',
                'zip_code' => '25436',
            ],
            [
                'street' => 'Kiebitzweg 2',
                'city' => 'Schenefeld',
                'zip_code' => '22869',
            ],
            [
                'street' => 'Yachthafen 1',
                'city' => 'Heiligenhafen',
                'zip_code' => '23774',
            ],
            [
                'street' => 'Poststrasse 7',
                'city' => 'Kappeln',
                'zip_code' => '24376',
            ],
            [
                'street' => 'Maria-Sybilla-Merian-Strasse 7',
                'city' => 'Tornesch',
                'zip_code' => '25436',
            ],
            [
                'street' => 'Am Markt 17',
                'city' => 'Wesselburen',
                'zip_code' => '25764',
            ],
            [
                'street' => 'Sandberg 71',
                'city' => 'Itzehoe',
                'zip_code' => '25524',
            ],
            [
                'street' => 'Grosse Strasse 69',
                'city' => 'Flensburg',
                'zip_code' => '24937',
            ],
            [
                'street' => 'Suderdamm 4',
                'city' => 'Heide',
                'zip_code' => '25746',
            ],
            [
                'street' => 'Kirchenstrasse 28',
                'city' => 'Preetz',
                'zip_code' => '24211',
            ],
            [
                'street' => 'Marktstrasse 2',
                'city' => 'Uetersen',
                'zip_code' => '25436',
            ],
            [
                'street' => 'Uhlenhorst 1',
                'city' => 'Schwarzenbek',
                'zip_code' => '21493',
            ],
            [
                'street' => 'Johannes-Ritter-Strasse 38',
                'city' => 'Geesthacht',
                'zip_code' => '21502',
            ],
            [
                'street' => 'Kolonnenweg 152',
                'city' => 'Schleswig',
                'zip_code' => '24837',
            ],
            [
                'street' => 'Lubecker Strasse 22',
                'city' => 'Eutin',
                'zip_code' => '23701',
            ],
            [
                'street' => 'Topferstrasse 1',
                'city' => 'Ratzeburg',
                'zip_code' => '23909',
            ],
        ];
    }

    public function getAll()
    {
        return $this->addresses;
    }

    public function getRandom()
    {
        return $this->addresses[array_rand($this->addresses)];
    }
}
