<?php

namespace PHPTerminalModulesTest;

use JasonGrimes\Paginator;
use PHPTerminal\Modules;
use PHPTerminal\Terminal;
use SleekDB\Cache;
use SleekDB\Classes\IoHelper;
use SleekDB\Store;

class Test extends Modules
{
    protected $terminal;

    protected $command;

    protected $testStore;

    public function init(Terminal $terminal = null, $command) : object
    {
        $this->terminal = $terminal;

        $this->command = $command;

        $this->testStore = new Store("test", $this->terminal->databaseDirectory, $this->terminal->storeConfiguration);

        return $this;
    }

    public function getCommands() : array
    {
        return
            [
                [
                    "availableAt"   => "enable",
                    "command"       => "",
                    "description"   => "Test Input",
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
                    "description"   => "Test output",
                    "function"      => ""
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test public method",
                    "description"   => "Shows output from public method.",
                    "function"      => "showTestPublicMethod"
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test protected method",
                    "description"   => "Shows output from protected method.",
                    "function"      => "show"
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test data single",
                    "description"   => "Shows single array test data to display different outputs.",
                    "function"      => "show"
                ],
                [
                    "availableAt"   => "enable",
                    "command"       => "show test data multiple paged",
                    "description"   => "Show test data using pagination. show test data multiple paged control will allow you to control display of next page.",
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
        $this->terminal->setCommandIgnoreChars(['.',':']);

        $this->terminal->config['modules']['test']['banner'] = 'PHPTerminal-modules-test is an test module for PHPTerminal with some test data.';

        $this->testStore->updateOrInsertMany($this->getTestDataMultiple());

        return $this;
    }

    public function onUninstall() : object
    {
        $this->testStore->deleteStore();

        return $this;
    }

    public function onActive() : object
    {
        echo "\nActivating test module. This message was echoed using onActive() method\n";

        return $this;
    }

    public function testInputData()
    {
        $inputData =
            $this->terminal->inputToArray(
                $this->getTestInputDataFields(),
                ['location' => ['Sydney', 'Melbourne', 'Perth']],
                $this->getTestInputDataFieldsDefaultValues(),
                $this->getTestInputDataFieldsData(),
                ['username' => true],
                3,
                false
            );

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

    private function getTestInputDataFields()
    {
        return
            [
                'username', 'first_name', 'last_name', 'location', 'password__secret'
            ];
    }

    private function getTestInputDataFieldsDefaultValues()
    {
        return
            [
                'location'     => 'Melbourne'
            ];
    }

    private function getTestInputDataFieldsData()
    {
        return
            [
                'username'          => 'oyeaussie',
                'first_name'        => 'oyeaussie',
                'last_name'         => 'oyeaussie',
                'location'          => 'Melbourne',
                'password'          => '123'
            ];
    }

    public function showTestPublicMethod()
    {
        echo "This is public method\n\n";

        return true;
    }

    protected function showTestProtectedMethod($args = [])
    {
        echo "This is protected method\n\n";

        print_r($args);

        return true;
    }

    protected function showTestDataSingle()
    {
        $this->terminal->addResponse(
            'Success!',
            0,
            [
                'data'  => $this->getTestDataSingle(),
                'data2' => 'test',
                'data3' =>
                [
                    'd31' => 'test1',
                    'd32' => 'test2'
                ]
            ]
        );

        return true;
    }

    protected function showTestDataMultiple()
    {
        $this->terminal->addResponse(
            'Success! Showing multiple rows data',
            0,
            ['data' => $this->testStore->findAll()],
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

    protected function showTestDataMultiplePaged($args)
    {
        //We are not sending data back to phpterminal as phpterminal default output addResponse does not support pagination.
        //addRespnose's job is to just dump the data in a table/list format. What data is provided to it depends on the command
        //You can instruct the command to show a particular page using the args that you pass. This command demonstrate that.
        if (isset($args[0]) && ((int) $args[0] === 0 || (int) $args[0] > 20)) {
            $this->terminal->addResponse('Limit value should be a number. Please provide correct value. Max is 20.', 1);

            return true;
        }

        if (isset($args[1]) && (int) $args[1] === 0) {
            $this->terminal->addResponse('Page number value should be a number. Please provide correct value!', 1);

            return true;
        }

        $itemsPerPage = $args[0] ?? 10;
        $currentPage = $args[1] ?? 1;

        if ($this->testStore->_getUseCache() === true) {
            $cacheTokenArray = ["count" => true];
            $cache = new Cache($this->testStore->getStorePath(), $cacheTokenArray, null);
            $cache->delete();
            $totalItems = $this->testStore->count();
            IoHelper::updateFileContent($this->testStore->getStorePath() . '_cnt.sdb', function() use ($totalItems) {
                return $totalItems;
            });
        } else {
            $totalItems = $this->testStore->count();
        }

        $paginator = new Paginator((int) $totalItems, (int) $itemsPerPage, (int) $currentPage);
        if ($paginator->getNumPages() > 3) {
            $paginator->setMaxPagesToShow($paginator->getNumPages());
        }

        $pages = $paginator->getPages();
        //The package paginator is designed to show minimum 3 pages worth of data, which is quite weird.
        //We have to add a dummy array to pages, so that the while look will work once.
        if (count($pages) === 0) {
            $pages[0] = [];
        }

        $pageCounter = ((int) $currentPage - 1);

        $headers =
            [
                'id', 'name', 'position', 'office', 'extn.', 'salary'
            ];
        $columns =
            [
                5,25,30,25,7,15
            ];

        while (isset($pages[$pageCounter])) {
            $items = $this->testStore->findAll(['id' => 'asc'], $itemsPerPage, ((int) $itemsPerPage * $pageCounter));

            array_walk($items, function(&$item) use ($headers) {
                $item = array_replace(array_flip($headers), $item);
                $item = array_values($item);
            });

            $table = new \cli\Table();
            $table->setHeaders($headers);
            $table->setRows($items);
            $table->setRenderer(new \cli\table\Ascii($columns));
            $table->display();

            $lastpage = false;
            $rowsCount = count($items);
            if (($pageCounter + 1) === count($pages)) {
                $rowsCounter = $totalItems;
                $lastpage = true;
            } else {
                $rowsCounter = ($rowsCount * ($pageCounter + 1));
            }
            \cli\line('%cShowing record : ' . $rowsCounter . '/' . $totalItems . '. Page : ' . ($pageCounter + 1) . '/' . count($pages) . '. ');
            if ($lastpage) {
                \cli\line('%w');
                return true;
            }
            \cli\line('%bHit space bar or n for next page, p for previous page, q to quit%w' . PHP_EOL);

            readline_callback_handler_install("", function () {});

            while (true) {
                $input = stream_get_contents(STDIN, 1);

                if (ord($input) == 32 || ord($input) == 110 || ord($input) == 78) {//Next space or n
                    $pageCounter++;

                    break;
                } else if (ord($input) == 112 || ord($input) == 80) {//Previous
                    $pageCounter--;

                    break;
                } else if (ord($input) == 113 || ord($input) == 81) {
                    readline_callback_handler_remove();
                    return true;
                }
            }

            readline_callback_handler_remove();
        }

        return true;
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