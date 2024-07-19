<?php

namespace PHPTerminalModulesTest;

use PHPTerminal\Modules;
use PHPTerminal\Terminal;

class Test extends Modules
{
    protected $terminal;

    protected $command;

    public function init(Terminal $terminal = null, $command) : object
    {
        $this->terminal = $terminal;

        $this->command = $command;

        return $this;
    }

    public function getCommands() : array
    {
        return
            [
                [
                    "availableAt"   => "enable",
                    "command"       => "",
                    "description"   => "Test Input commands",
                    "function"      => ""
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "test input data",
                    "description"   => "Example on how data can be input and given back as an array.",
                    "function"      => "testInputData"
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "",
                    "description"   => "",
                    "function"      => ""
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "",
                    "description"   => "Test output commands",
                    "function"      => ""
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test data single",
                    "description"   => "Shows single array test data to display different outputs.",
                    "function"      => "show"
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test data multiple",
                    "description"   => "Shows multiple array (rows) test data to display different outputs.",
                    "function"      => "show"
                ]
            ];
    }

    public function onInstall() : object
    {
        $this->terminal->config['modules']['test']['banner'] = 'PHPTerminal-modules-test is an test module for PHPTerminal with some test data.';

        return $this;
    }

    public function testInputData()
    {
        $inputData = $this->terminal->inputToArray($this->getTestInputDataFields());

        if ($inputData) {
            $this->terminal->addResponse(
                strtoupper('note: Test output will be returned as array to your calling method.'),
                2,
                $this->getTestDataSingle($inputData)
            );
            return true;
        }

        return false;
    }

    protected function showTestDataSingle()
    {
        $this->terminal->addResponse(
            '',
            0,
            ['data' => $this->getTestDataSingle()]
        );

        return true;
    }

    protected function showTestDataMultiple()
    {
        $this->terminal->addResponse(
            '',
            0,
            ['data' => $this->getTestDataMultiple()],
            true,
            [
                'name', 'position', 'office', 'extn.', 'salary'
            ],
            [
                25,30,25,7,15
            ]
        );

        return true;
    }

    private function getTestInputDataFields()
    {
        return
            [
                'username', 'first_name', 'last_name', 'password__secret'
            ];
    }

    private function getTestDataSingle(array $inputData = null)
    {
        if ($inputData) {
            return ['input data' => $inputData];
        }

        return
            [
                "running configuration" =>
                    [
                        "_id"               => 1,
                        "hostname"          => "phpterminal",
                        "banner"            => "Welcome to PHP Terminal!\nType help or ? (question mark) for help.\n",
                        "active_module"     => "test",
                        "modules"           =>
                        [
                            "base"              =>
                            [
                                "name"              => "base",
                                "package_name"      => "phpterminal/phpterminal",
                                "description"       => "PHP Terminal Base Module",
                                "location"          => "/var/www/html/projects/phpterminal/src/BaseModules/",
                                "version"           => "viaGit"
                            ],
                            "firewall"          =>
                            [
                                "name"              => "firewall",
                                "package_name"      => "phpterminal/phpterminal-modules-firewall",
                                "description"       => "PHPTerminal-modules-firewall is an firewall module for PHPTerminal to manage PHPFirewall library.",
                                "location"          => "/var/www/html/projects/phpterminal/vendor/phpterminal/phpterminal-modules-firewall/src/",
                                "version"           => "0.1.0"
                            ],
                            "test"              =>
                            [
                                "name"              => "test",
                                "package_name"      => "phpterminal/phpterminal-modules-test",
                                "description"       => "PHPTerminal-modules-test is an test module for PHPTerminal with some test data.",
                                "location"          => "/var/www/html/projects/phpterminal/vendor/phpterminal/phpterminal-modules-test/src/",
                                "version"           => "0.1.0"
                            ]
                        ],
                        "plugins"           =>
                        [
                            "auth"              =>
                            [
                                "name"              => "auth",
                                "package_name"      => "phpterminal/phpterminal-plugins-auth",
                                "description"       => "PHPTerminal-plugins-auth is an authentication plugin for phpterminal application.",
                                "class"             => "PHPTerminalPluginsAuth\\Auth",
                                "version"           => "0.1.0",
                                "settings"          =>
                                [
                                    "cost"              => 4,
                                    "hash"              => "PASSWORD_BCRYPT",
                                    "canResetPasswd"    => true
                                ]
                            ]
                        ],
                        "updatedAt"         => 1721273518
                    ]
            ];
    }

    private function getTestDataMultiple()
    {
        return
            [
                [
                    "name" => "Tiger Nixon",
                    "position" => "System Architect",
                    "office" => "Edinburgh",
                    "extn." => "5421",
                    "salary" => "$320,800"
                ],
                [
                    "name" => "Garrett Winters",
                    "position" => "Accountant",
                    "office" => "Tokyo",
                    "extn." => "8422",
                    "salary" => "$170,750"
                ],
                [
                    "name" => "Ashton Cox",
                    "position" => "Junior Technical Author",
                    "office" => "San Francisco",
                    "extn." => "1562",
                    "salary" => "$86,000"
                ],
                [
                    "name" => "Cedric Kelly",
                    "position" => "Senior Javascript Developer",
                    "office" => "Edinburgh",
                    "extn." => "6224",
                    "salary" => "$433,060"
                ],
                [
                    "name" => "Airi Satou",
                    "position" => "Accountant",
                    "office" => "Tokyo",
                    "extn." => "5407",
                    "salary" => "$162,700"
                ],
                [
                    "name" => "Brielle Williamson",
                    "position" => "Integration Specialist",
                    "office" => "New York",
                    "extn." => "4804",
                    "salary" => "$372,000"
                ],
                [
                    "name" => "Herrod Chandler",
                    "position" => "Sales Assistant",
                    "office" => "San Francisco",
                    "extn." => "9608",
                    "salary" => "$137,500"
                ],
                [
                    "name" => "Rhona Davidson",
                    "position" => "Integration Specialist",
                    "office" => "Tokyo",
                    "extn." => "6200",
                    "salary" => "$327,900"
                ],
                [
                    "name" => "Colleen Hurst",
                    "position" => "Javascript Developer",
                    "office" => "San Francisco",
                    "extn." => "2360",
                    "salary" => "$205,500"
                ],
                [
                    "name" => "Sonya Frost",
                    "position" => "Software Engineer",
                    "office" => "Edinburgh",
                    "extn." => "1667",
                    "salary" => "$103,600"
                ],
                [
                    "name" => "Jena Gaines",
                    "position" => "Office Manager",
                    "office" => "London",
                    "extn." => "3814",
                    "salary" => "$90,560"
                ],
                [
                    "name" => "Quinn Flynn",
                    "position" => "Support Lead",
                    "office" => "Edinburgh",
                    "extn." => "9497",
                    "salary" => "$342,000"
                ],
                [
                    "name" => "Charde Marshall",
                    "position" => "Regional Director",
                    "office" => "San Francisco",
                    "extn." => "6741",
                    "salary" => "$470,600"
                ],
                [
                    "name" => "Haley Kennedy",
                    "position" => "Senior Marketing Designer",
                    "office" => "London",
                    "extn." => "3597",
                    "salary" => "$313,500"
                ],
                [
                    "name" => "Tatyana Fitzpatrick",
                    "position" => "Regional Director",
                    "office" => "London",
                    "extn." => "1965",
                    "salary" => "$385,750"
                ],
                [
                    "name" => "Michael Silva",
                    "position" => "Marketing Designer",
                    "office" => "London",
                    "extn." => "1581",
                    "salary" => "$198,500"
                ],
                [
                    "name" => "Paul Byrd",
                    "position" => "Chief Financial Officer (CFO)",
                    "office" => "New York",
                    "extn." => "3059",
                    "salary" => "$725,000"
                ],
                [
                    "name" => "Gloria Little",
                    "position" => "Systems Administrator",
                    "office" => "New York",
                    "extn." => "1721",
                    "salary" => "$237,500"
                ],
                [
                    "name" => "Bradley Greer",
                    "position" => "Software Engineer",
                    "office" => "London",
                    "extn." => "2558",
                    "salary" => "$132,000"
                ],
                [
                    "name" => "Dai Rios",
                    "position" => "Personnel Lead",
                    "office" => "Edinburgh",
                    "extn." => "2290",
                    "salary" => "$217,500"
                ],
                [
                    "name" => "Jenette Caldwell",
                    "position" => "Development Lead",
                    "office" => "New York",
                    "extn." => "1937",
                    "salary" => "$345,000"
                ],
                [
                    "name" => "Yuri Berry",
                    "position" => "Chief Marketing Officer (CMO)",
                    "office" => "New York",
                    "extn." => "6154",
                    "salary" => "$675,000"
                ],
                [
                    "name" => "Caesar Vance",
                    "position" => "Pre-Sales Support",
                    "office" => "New York",
                    "extn." => "8330",
                    "salary" => "$106,450"
                ],
                [
                    "name" => "Doris Wilder",
                    "position" => "Sales Assistant",
                    "office" => "Sydney",
                    "extn." => "3023",
                    "salary" => "$85,600"
                ],
                [
                    "name" => "Angelica Ramos",
                    "position" => "Chief Executive Officer (CEO)",
                    "office" => "London",
                    "extn." => "5797",
                    "salary" => "$1,200,000"
                ],
                [
                    "name" => "Gavin Joyce",
                    "position" => "Developer",
                    "office" => "Edinburgh",
                    "extn." => "8822",
                    "salary" => "$92,575"
                ],
                [
                    "name" => "Jennifer Chang",
                    "position" => "Regional Director",
                    "office" => "Singapore",
                    "extn." => "9239",
                    "salary" => "$357,650"
                ],
                [
                    "name" => "Brenden Wagner",
                    "position" => "Software Engineer",
                    "office" => "San Francisco",
                    "extn." => "1314",
                    "salary" => "$206,850"
                ],
                [
                    "name" => "Fiona Green",
                    "position" => "Chief Operating Officer (COO)",
                    "office" => "San Francisco",
                    "extn." => "2947",
                    "salary" => "$850,000"
                ],
                [
                    "name" => "Shou Itou",
                    "position" => "Regional Marketing",
                    "office" => "Tokyo",
                    "extn." => "8899",
                    "salary" => "$163,000"
                ],
                [
                    "name" => "Michelle House",
                    "position" => "Integration Specialist",
                    "office" => "Sydney",
                    "extn." => "2769",
                    "salary" => "$95,400"
                ],
                [
                    "name" => "Suki Burks",
                    "position" => "Developer",
                    "office" => "London",
                    "extn." => "6832",
                    "salary" => "$114,500"
                ],
                [
                    "name" => "Prescott Bartlett",
                    "position" => "Technical Author",
                    "office" => "London",
                    "extn." => "3606",
                    "salary" => "$145,000"
                ],
                [
                    "name" => "Gavin Cortez",
                    "position" => "Team Leader",
                    "office" => "San Francisco",
                    "extn." => "2860",
                    "salary" => "$235,500"
                ],
                [
                    "name" => "Martena Mccray",
                    "position" => "Post-Sales support",
                    "office" => "Edinburgh",
                    "extn." => "8240",
                    "salary" => "$324,050"
                ],
                [
                    "name" => "Unity Butler",
                    "position" => "Marketing Designer",
                    "office" => "San Francisco",
                    "extn." => "5384",
                    "salary" => "$85,675"
                ],
                [
                    "name" => "Howard Hatfield",
                    "position" => "Office Manager",
                    "office" => "San Francisco",
                    "extn." => "7031",
                    "salary" => "$164,500"
                ],
                [
                    "name" => "Hope Fuentes",
                    "position" => "Secretary",
                    "office" => "San Francisco",
                    "extn." => "6318",
                    "salary" => "$109,850"
                ],
                [
                    "name" => "Vivian Harrell",
                    "position" => "Financial Controller",
                    "office" => "San Francisco",
                    "extn." => "9422",
                    "salary" => "$452,500"
                ],
                [
                    "name" => "Timothy Mooney",
                    "position" => "Office Manager",
                    "office" => "London",
                    "extn." => "7580",
                    "salary" => "$136,200"
                ],
                [
                    "name" => "Jackson Bradshaw",
                    "position" => "Director",
                    "office" => "New York",
                    "extn." => "1042",
                    "salary" => "$645,750"
                ],
                [
                    "name" => "Olivia Liang",
                    "position" => "Support Engineer",
                    "office" => "Singapore",
                    "extn." => "2120",
                    "salary" => "$234,500"
                ],
                [
                    "name" => "Bruno Nash",
                    "position" => "Software Engineer",
                    "office" => "London",
                    "extn." => "6222",
                    "salary" => "$163,500"
                ],
                [
                    "name" => "Sakura Yamamoto",
                    "position" => "Support Engineer",
                    "office" => "Tokyo",
                    "extn." => "9383",
                    "salary" => "$139,575"
                ],
                [
                    "name" => "Thor Walton",
                    "position" => "Developer",
                    "office" => "New York",
                    "extn." => "8327",
                    "salary" => "$98,540"
                ],
                [
                    "name" => "Finn Camacho",
                    "position" => "Support Engineer",
                    "office" => "San Francisco",
                    "extn." => "2927",
                    "salary" => "$87,500"
                ],
                [
                    "name" => "Serge Baldwin",
                    "position" => "Data Coordinator",
                    "office" => "Singapore",
                    "extn." => "8352",
                    "salary" => "$138,575"
                ],
                [
                    "name" => "Zenaida Frank",
                    "position" => "Software Engineer",
                    "office" => "New York",
                    "extn." => "7439",
                    "salary" => "$125,250"
                ],
                [
                    "name" => "Zorita Serrano",
                    "position" => "Software Engineer",
                    "office" => "San Francisco",
                    "extn." => "4389",
                    "salary" => "$115,000"
                ],
                [
                    "name" => "Jennifer Acosta",
                    "position" => "Junior Javascript Developer",
                    "office" => "Edinburgh",
                    "extn." => "3431",
                    "salary" => "$75,650"
                ],
                [
                    "name" => "Cara Stevens",
                    "position" => "Sales Assistant",
                    "office" => "New York",
                    "extn." => "3990",
                    "salary" => "$145,600"
                ],
                [
                    "name" => "Hermione Butler",
                    "position" => "Regional Director",
                    "office" => "London",
                    "extn." => "1016",
                    "salary" => "$356,250"
                ],
                [
                    "name" => "Lael Greer",
                    "position" => "Systems Administrator",
                    "office" => "London",
                    "extn." => "6733",
                    "salary" => "$103,500"
                ],
                [
                    "name" => "Jonas Alexander",
                    "position" => "Developer",
                    "office" => "San Francisco",
                    "extn." => "8196",
                    "salary" => "$86,500"
                ],
                [
                    "name" => "Shad Decker",
                    "position" => "Regional Director",
                    "office" => "Edinburgh",
                    "extn." => "6373",
                    "salary" => "$183,000"
                ],
                [
                    "name" => "Michael Bruce",
                    "position" => "Javascript Developer",
                    "office" => "Singapore",
                    "extn." => "5384",
                    "salary" => "$183,000"
                ],
                [
                    "name" => "Donna Snider",
                    "position" => "Customer Support",
                    "office" => "New York",
                    "extn." => "4226",
                    "salary" => "$112,000"
                ]
            ];
    }
}