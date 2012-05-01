<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */

class BV_Form_Element_Timezone
{

    public function __construct()
    {
    }
    
    public static function getElement()
    {
        $timezone = new Zend_Form_Element_Select('timezone');
        $timezone->setLabel('Timezone')
                    ->setRequired(true);
        

        $timezone->addMultiOptions(array(
            array('key'=>'America/Adak', 'value'=>'America/Adak'),
            array('key'=>'America/Anchorage', 'value'=>'America/Anguilla'),
            array('key'=>'America/Antigua', 'value'=>'America/Antigua'),
            array('key'=>'America/Araguaina', 'value'=>'America/Araguaina'),
            array('key'=>'America/Argentina/Buenos_Aires', 'value'=>'America/Argentina/Buenos Aires'),
            array('key'=>'America/Argentina/Catamarca', 'value'=>'America/Argentina/Catamarca'),
            array('key'=>'America/Argentina/ComodRivadavia', 'value'=>'America/Argentina/ComodRivadavia'),
            array('key'=>'America/Argentina/Cordoba', 'value'=>'America/Argentina/Cordoba'),
            array('key'=>'America/Argentina/Jujuy', 'value'=>'America/Argentina/Jujuy'),
            array('key'=>'America/Argentina/La_Rioja', 'value'=>'America/Argentina/Mendoza'),
            array('key'=>'America/Argentina/Rio_Gallegos', 'value'=>'America/Argentina/Rio Gallegos'),
            array('key'=>'America/Argentina/San_Juan', 'value'=>'America/Argentina/San Juan'),
            array('key'=>'America/Argentina/San_Luis', 'value'=>'America/Argentina/San Luis'),
            array('key'=>'America/Argentina/Tucuman', 'value'=>'America/Argentina/Ushuaia'),
            array('key'=>'America/Aruba', 'value'=>'America/Asuncion'),
            array('key'=>'America/Atikokan', 'value'=>'America/Atikokan'),
            array('key'=>'America/Atka', 'value'=>'America/Bahia'),
            array('key'=>'America/Barbados', 'value'=>'America/Barbados'),
            array('key'=>'America/Belem', 'value'=>'America/Belem'),
            array('key'=>'America/Belize', 'value'=>'America/Belize'),
            array('key'=>'America/Blanc-Sablon', 'value'=>'America/Blanc-Sablon'),
            array('key'=>'America/Boa_Vista', 'value'=>'America/Boa Vista'),
            array('key'=>'America/Bogota', 'value'=>'America/Bogota'),
            array('key'=>'America/Boise', 'value'=>'America/Boise'),
            array('key'=>'America/Buenos_Aires', 'value'=>'America/Buenos Aires'),
            array('key'=>'America/Cambridge_Bay', 'value'=>'America/Cambridge Bay'),
            array('key'=>'America/Campo_Grande', 'value'=>'America/Campo Grande'),
            array('key'=>'America/Cancun', 'value'=>'America/Cancun'),
            array('key'=>'America/Caracas', 'value'=>'America/Caracas'),
            array('key'=>'America/Catamarca', 'value'=>'America/Catamarca'),
            array('key'=>'America/Cayenne', 'value'=>'America/Cayenne'),
            array('key'=>'America/Cayman', 'value'=>'America/Cayman'),
            array('key'=>'America/Chicago', 'value'=>'America/Chicago'),
            array('key'=>'America/Chihuahua', 'value'=>'America/Chihuahua'),
            array('key'=>'America/Coral_Harbour', 'value'=>'America/Coral Harbour'),
            array('key'=>'America/Cordoba', 'value'=>'America/Cordoba'),
            array('key'=>'America/Costa_Rica', 'value'=>'America/Costa Rica'),
            array('key'=>'America/Cuiaba', 'value'=>'America/Cuiaba'),
            array('key'=>'America/Curacao', 'value'=>'America/Curacao'),
            array('key'=>'America/Danmarkshavn', 'value'=>'America/Danmarkshavn'),
            array('key'=>'America/Dawson', 'value'=>'America/Dawson'),
            array('key'=>'America/Dawson_Creek', 'value'=>'America/Dawson Creek'),
            array('key'=>'America/Denver', 'value'=>'America/Denver'),
            array('key'=>'America/Detroit', 'value'=>'America/Detroit'),
            array('key'=>'America/Dominica', 'value'=>'America/Dominica'),
            array('key'=>'America/Edmonton', 'value'=>'America/Edmonton'),
            array('key'=>'America/Eirunepe', 'value'=>'America/Eirunepe'),
            array('key'=>'America/El_Salvador', 'value'=>'America/El Salvador'),
            array('key'=>'America/Ensenada', 'value'=>'America/Ensenada'),
            array('key'=>'America/Fort_Wayne', 'value'=>'America/Fort Wayne'),
            array('key'=>'America/Fortaleza', 'value'=>'America/Fortaleza'),
            array('key'=>'America/Glace_Bay', 'value'=>'America/Glace Bay'),
            array('key'=>'America/Godthab', 'value'=>'America/Godthab'),
            array('key'=>'America/Goose_Bay', 'value'=>'America/Goose Bay'),
            array('key'=>'America/Grand_Turk', 'value'=>'America/Grand Turk'),
            array('key'=>'America/Grenada', 'value'=>'America/Grenada'),
            array('key'=>'America/Guadeloupe', 'value'=>'America/Guadeloupe'),
            array('key'=>'America/Guatemala', 'value'=>'America/Guatemala'),
            array('key'=>'America/Guayaquil', 'value'=>'America/Guayaquil'),
            array('key'=>'America/Guyana', 'value'=>'America/Guyana'),
            array('key'=>'America/Halifax', 'value'=>'America/Halifax'),
            array('key'=>'America/Havana', 'value'=>'America/Havana'),
            array('key'=>'America/Hermosillo', 'value'=>'America/Hermosillo'),
            array('key'=>'America/Indiana/Indianapolis', 'value'=>'America/Indiana/Indianapolis'),
            array('key'=>'America/Indiana/Knox', 'value'=>'America/Indiana/Knox'),
            array('key'=>'America/Indiana/Marengo', 'value'=>'America/Indiana/Marengo'),
            array('key'=>'America/Indiana/Petersburg', 'value'=>'America/Indiana/Petersburg'),
            array('key'=>'America/Indiana/Tell_City', 'value'=>'America/Indiana/Tell City'),
            array('key'=>'America/Indiana/Vevay', 'value'=>'America/Indiana/Vevay'),
            array('key'=>'America/Indiana/Vincennes', 'value'=>'America/Indiana/Vincennes'),
            array('key'=>'America/Indiana/Winamac', 'value'=>'America/Indiana/Winamac'),
            array('key'=>'America/Indianapolis', 'value'=>'America/Indianapolis'),
            array('key'=>'America/Inuvik', 'value'=>'America/Inuvik'),
            array('key'=>'America/Iqaluit', 'value'=>'America/Iqaluit'),
            array('key'=>'America/Jamaica', 'value'=>'America/Jamaica'),
            array('key'=>'America/Jujuy', 'value'=>'America/Jujuy'),
            array('key'=>'America/Juneau', 'value'=>'America/Juneau'),
            array('key'=>'America/Kentucky/Louisville', 'value'=>'America/Kentucky/Louisville'),
            array('key'=>'America/Kentucky/Monticello', 'value'=>'America/Kentucky/Monticello'),
            array('key'=>'America/Knox_IN', 'value'=>'America/Knox IN'),
            array('key'=>'America/La_Paz', 'value'=>'America/La Paz'),
            array('key'=>'America/Lima', 'value'=>'America/Lima'),
            array('key'=>'America/Los_Angeles', 'value'=>'America/Los Angeles'),
            array('key'=>'America/Louisville', 'value'=>'America/Louisville'),
            array('key'=>'America/Maceio', 'value'=>'America/Maceio'),
            array('key'=>'America/Managua', 'value'=>'America/Managua'),
            array('key'=>'America/Manaus', 'value'=>'America/Manaus'),
            array('key'=>'America/Marigot', 'value'=>'America/Marigot'),
            array('key'=>'America/Martinique', 'value'=>'America/Martinique'),
            array('key'=>'America/Mazatlan', 'value'=>'America/Mazatlan'),
            array('key'=>'America/Mendoza', 'value'=>'America/Mendoza'),
            array('key'=>'America/Menominee', 'value'=>'America/Menominee'),
            array('key'=>'America/Merida', 'value'=>'America/Merida'),
            array('key'=>'America/Mexico_City', 'value'=>'America/Mexico City'),
            array('key'=>'America/Miquelon', 'value'=>'America/Miquelon'),
            array('key'=>'America/Moncton', 'value'=>'America/Moncton'),
            array('key'=>'America/Monterrey', 'value'=>'America/Monterrey'),
            array('key'=>'America/Montevideo', 'value'=>'America/Montevideo'),
            array('key'=>'America/Montreal', 'value'=>'America/Montreal'),
            array('key'=>'America/Montserrat', 'value'=>'America/Montserrat'),
            array('key'=>'America/Nassau', 'value'=>'America/Nassau'),
            array('key'=>'America/New_York', 'value'=>'America/New York'),
            array('key'=>'America/Nipigon', 'value'=>'America/Nipigon'),
            array('key'=>'America/Nome', 'value'=>'America/Nome'),
            array('key'=>'America/Noronha', 'value'=>'America/Noronha'),
            array('key'=>'America/North_Dakota/Center', 'value'=>'America/North_Dakota/Center'),
            array('key'=>'America/North_Dakota/New_Salem', 'value'=>'America/North_Dakota/New Salem'),
            array('key'=>'America/Panama', 'value'=>'America/Panama'),
            array('key'=>'America/Pangnirtung', 'value'=>'America/Pangnirtung'),
            array('key'=>'America/Paramaribo', 'value'=>'America/Paramaribo'),
            array('key'=>'America/Phoenix', 'value'=>'America/Phoenix'),
            array('key'=>'America/Port-au-Prince', 'value'=>'America/Port au Prince'),
            array('key'=>'America/Port_of_Spain', 'value'=>'America/Port of Spain'),
            array('key'=>'America/Porto_Acre', 'value'=>'America/Porto Acre'),
            array('key'=>'America/Porto_Velho', 'value'=>'America/Porto Velho'),
            array('key'=>'America/Puerto_Rico', 'value'=>'America/Puerto Rico'),
            array('key'=>'America/Rainy_River', 'value'=>'America/Rainy River'),
            array('key'=>'America/Rankin_Inlet', 'value'=>'America/Rankin Inlet'),
            array('key'=>'America/Recife', 'value'=>'America/Recife'),
            array('key'=>'America/Regina', 'value'=>'America/Regina'),
            array('key'=>'America/Resolute', 'value'=>'America/Resolute'),
            array('key'=>'America/Rio_Branco', 'value'=>'America/Rio Branco'),
            array('key'=>'America/Rosario', 'value'=>'America/Rosario'),
            array('key'=>'America/Santiago', 'value'=>'America/Santiago'),
            array('key'=>'America/Santo_Domingo', 'value'=>'America/Santo Domingo'),
            array('key'=>'America/Sao_Paulo', 'value'=>'America/Sao Paulo'),
            array('key'=>'America/Scoresbysund', 'value'=>'America/Scoresbysund'),
            array('key'=>'America/Shiprock', 'value'=>'America/Shiprock'),
            array('key'=>'America/St_Barthelemy', 'value'=>'America/St Barthelemy'),
            array('key'=>'America/St_Johns', 'value'=>'America/St Johns'),
            array('key'=>'America/St_Kitts', 'value'=>'America/St Kitts'),
            array('key'=>'America/St_Lucia', 'value'=>'America/St Lucia'),
            array('key'=>'America/St_Thomas', 'value'=>'America/St Thomas'),
            array('key'=>'America/St_Vincent', 'value'=>'America/St Vincent'),
            array('key'=>'America/Swift_Current', 'value'=>'America/Swift Current'),
            array('key'=>'America/Tegucigalpa', 'value'=>'America/Tegucigalpa'),
            array('key'=>'America/Thule', 'value'=>'America/Thule'),
            array('key'=>'America/Thunder_Bay', 'value'=>'America/Thunder Bay'),
            array('key'=>'America/Tijuana', 'value'=>'America/Tijuana'),
            array('key'=>'America/Toronto', 'value'=>'America/Toronto'),
            array('key'=>'America/Tortola', 'value'=>'America/Tortola'),
            array('key'=>'America/Vancouver', 'value'=>'America/Vancouver'),
            array('key'=>'America/Virgin', 'value'=>'America/Virgin'),
            array('key'=>'America/Whitehorse', 'value'=>'America/Whitehorse'),
            array('key'=>'America/Winnipeg', 'value'=>'America/Winnipeg'),
            array('key'=>'America/Yakutat', 'value'=>'America/Yakutat'),
            array('key'=>'America/Yellowknife', 'value'=>'America/Yellowknife'),
            array('key'=>'Antarctica/Casey', 'value'=>'Antarctica/Casey'),
            array('key'=>'Antarctica/Davis', 'value'=>'Antarctica/Davis'),
            array('key'=>'Antarctica/DumontDUrville', 'value'=>'Antarctica/DumontDUrville'),
            array('key'=>'Antarctica/Mawson', 'value'=>'Antarctica/Mawson'),
            array('key'=>'Antarctica/McMurdo', 'value'=>'Antarctica/McMurdo'),
            array('key'=>'Antarctica/Palmer', 'value'=>'Antarctica/Palmer'),
            array('key'=>'Antarctica/Rothera', 'value'=>'Antarctica/Rothera'),
            array('key'=>'Antarctica/South_Pole', 'value'=>'Antarctica/South Pole'),
            array('key'=>'Antarctica/Syowa', 'value'=>'Antarctica/Syowa'),
            array('key'=>'Antarctica/Vostok', 'value'=>'Antarctica/Vostok'),
            array('key'=>'Arctic/Longyearbyen', 'value'=>'Arctic/Longyearbyen'),
            array('key'=>'Asia/Aden', 'value'=>'Asia/Aden'),
            array('key'=>'Asia/Almaty', 'value'=>'Asia/Almaty'),
            array('key'=>'Asia/Amman', 'value'=>'Asia/Amman'),
            array('key'=>'Asia/Anadyr', 'value'=>'Asia/Anadyr'),
            array('key'=>'Asia/Aqtau', 'value'=>'Asia/Aqtau'),
            array('key'=>'Asia/Aqtobe', 'value'=>'Asia/Aqtobe'),
            array('key'=>'Asia/Ashgabat', 'value'=>'Asia/Ashgabat'),
            array('key'=>'Asia/Ashkhabad', 'value'=>'Asia/Ashkhabad'),
            array('key'=>'Asia/Baghdad', 'value'=>'Asia/Baghdad'),
            array('key'=>'Asia/Bahrain', 'value'=>'Asia/Bahrain'),
            array('key'=>'Asia/Baku', 'value'=>'Asia/Baku'),
            array('key'=>'Asia/Bangkok', 'value'=>'Asia/Bangkok'),
            array('key'=>'Asia/Beirut', 'value'=>'Asia/Beirut'),
            array('key'=>'Asia/Bishkek', 'value'=>'Asia/Bishkek'),
            array('key'=>'Asia/Brunei', 'value'=>'Asia/Brunei'),
            array('key'=>'Asia/Calcutta', 'value'=>'Asia/Calcutta'),
            array('key'=>'Asia/Choibalsan', 'value'=>'Asia/Choibalsan'),
            array('key'=>'Asia/Chongqing', 'value'=>'Asia/Chongqing'),
            array('key'=>'Asia/Chungking', 'value'=>'Asia/Chungking'),
            array('key'=>'Asia/Colombo', 'value'=>'Asia/Colombo'),
            array('key'=>'Asia/Dacca', 'value'=>'Asia/Dacca'),
            array('key'=>'Asia/Damascus', 'value'=>'Asia/Damascus'),
            array('key'=>'Asia/Dhaka', 'value'=>'Asia/Dhaka'),
            array('key'=>'Asia/Dili', 'value'=>'Asia/Dili'),
            array('key'=>'Asia/Dubai', 'value'=>'Asia/Dubai'),
            array('key'=>'Asia/Dushanbe', 'value'=>'Asia/Dushanbe'),
            array('key'=>'Asia/Gaza', 'value'=>'Asia/Gaza'),
            array('key'=>'Asia/Harbin', 'value'=>'Asia/Harbin'),
            array('key'=>'Asia/Ho_Chi_Minh', 'value'=>'Asia/Ho Chi Minh'),
            array('key'=>'Asia/Hong_Kong', 'value'=>'Asia/Hong Kong'),
            array('key'=>'Asia/Hovd', 'value'=>'Asia/Irkutsk'),
            array('key'=>'Asia/Istanbul', 'value'=>'Asia/Istanbul'),
            array('key'=>'Asia/Jakarta', 'value'=>'Asia/Jakarta'),
            array('key'=>'Asia/Jayapura', 'value'=>'Asia/Jayapura'),
            array('key'=>'Asia/Jerusalem', 'value'=>'Asia/Jerusalem'),
            array('key'=>'Asia/Kabul', 'value'=>'Asia/Kabul'),
            array('key'=>'Asia/Kamchatka', 'value'=>'Asia/Kamchatka'),
            array('key'=>'Asia/Karachi', 'value'=>'Asia/Karachi'),
            array('key'=>'Asia/Kashgar', 'value'=>'Asia/Kashgar'),
            array('key'=>'Asia/Katmandu', 'value'=>'Asia/Katmandu'),
            array('key'=>'Asia/Kolkata', 'value'=>'Asia/Kolkata'),
            array('key'=>'Asia/Krasnoyarsk', 'value'=>'Asia/Krasnoyarsk'),
            array('key'=>'Asia/Kuala_Lumpur', 'value'=>'Asia/Kuala Lumpur'),
            array('key'=>'Asia/Kuching', 'value'=>'Asia/Kuching'),
            array('key'=>'Asia/Kuwait', 'value'=>'Asia/Kuwait'),
            array('key'=>'Asia/Macao', 'value'=>'Asia/Macao'),
            array('key'=>'Asia/Macau', 'value'=>'Asia/Macau'),
            array('key'=>'Asia/Magadan', 'value'=>'Asia/Magadan'),
            array('key'=>'Asia/Makassar', 'value'=>'Asia/Makassar'),
            array('key'=>'Asia/Manila', 'value'=>'Asia/Manila'),
            array('key'=>'Asia/Muscat', 'value'=>'Asia/Muscat'),
            array('key'=>'Asia/Nicosia', 'value'=>'Asia/Nicosia'),
            array('key'=>'Asia/Novosibirsk', 'value'=>'Asia/Novosibirsk'),
            array('key'=>'Asia/Omsk', 'value'=>'Asia/Omsk'),
            array('key'=>'Asia/Oral', 'value'=>'Asia/Oral'),
            array('key'=>'Asia/Phnom_Penh', 'value'=>'Asia/Phnom_Penh'),
            array('key'=>'Asia/Pontianak', 'value'=>'Asia/Pontianak'),
            array('key'=>'Asia/Pyongyang', 'value'=>'Asia/Pyongyang'),
            array('key'=>'Asia/Qatar', 'value'=>'Asia/Qatar'),
            array('key'=>'Asia/Qyzylorda', 'value'=>'Asia/Qyzylorda'),
            array('key'=>'Asia/Rangoon', 'value'=>'Asia/Rangoon'),
            array('key'=>'Asia/Riyadh', 'value'=>'Asia/Riyadh'),
            array('key'=>'Asia/Saigon', 'value'=>'Asia/Saigon'),
            array('key'=>'Asia/Sakhalin', 'value'=>'Asia/Sakhalin'),
            array('key'=>'Asia/Samarkand', 'value'=>'Asia/Samarkand'),
            array('key'=>'Asia/Seoul', 'value'=>'Asia/Seoul'),
            array('key'=>'Asia/Shanghai', 'value'=>'Asia/Shanghai'),
            array('key'=>'Asia/Singapore', 'value'=>'Asia/Singapore'),
            array('key'=>'Asia/Taipei', 'value'=>'Asia/Taipei'),
            array('key'=>'Asia/Tashkent', 'value'=>'Asia/Tashkent'),
            array('key'=>'Asia/Tbilisi', 'value'=>'Asia/Tbilisi'),
            array('key'=>'Asia/Tehran', 'value'=>'Asia/Tehran'),
            array('key'=>'Asia/Tel_Aviv', 'value'=>'Asia/Tel Aviv'),
            array('key'=>'Asia/Thimbu', 'value'=>'Asia/Thimbu'),
            array('key'=>'Asia/Thimphu', 'value'=>'Asia/Thimphu'),
            array('key'=>'Asia/Tokyo', 'value'=>'Asia/Tokyo'),
            array('key'=>'Asia/Ujung_Pandang', 'value'=>'Asia/Ujung Pandang'),
            array('key'=>'Asia/Ulaanbaatar', 'value'=>'Asia/Ulaanbaatar'),
            array('key'=>'Asia/Ulan_Bator', 'value'=>'Asia/Ulan Bator'),
            array('key'=>'Asia/Urumqi', 'value'=>'Asia/Urumqi'),
            array('key'=>'Asia/Vientiane', 'value'=>'Asia/Vientiane'),
            array('key'=>'Asia/Vladivostok', 'value'=>'Asia/Vladivostok'),
            array('key'=>'Asia/Yakutsk', 'value'=>'Asia/Yakutsk'),
            array('key'=>'Asia/Yekaterinburg', 'value'=>'Asia/Yekaterinburg'),
            array('key'=>'Asia/Yerevan', 'value'=>'Asia/Yerevan'),
            array('key'=>'Atlantic/Azores', 'value'=>'Atlantic/Azores'),
            array('key'=>'Atlantic/Bermuda', 'value'=>'Atlantic/Bermuda'),
            array('key'=>'Atlantic/Canary', 'value'=>'Atlantic/Canary'),
            array('key'=>'Atlantic/Cape_Verde', 'value'=>'Atlantic/Cape Verde'),
            array('key'=>'Atlantic/Faeroe', 'value'=>'Atlantic/Faeroe'),
            array('key'=>'Atlantic/Faroe', 'value'=>'Atlantic/Faroe'),
            array('key'=>'Atlantic/Jan_Mayen', 'value'=>'Atlantic/Jan Mayen'),
            array('key'=>'Atlantic/Madeira', 'value'=>'Atlantic/Madeira'),
            array('key'=>'Atlantic/Reykjavik', 'value'=>'Atlantic/Reykjavik'),
            array('key'=>'Atlantic/South_Georgia', 'value'=>'Atlantic/South Georgia'),
            array('key'=>'Atlantic/St_Helena', 'value'=>'Atlantic/St Helena'),
            array('key'=>'Atlantic/Stanley', 'value'=>'Atlantic/Stanley'),
            array('key'=>'Australia/ACT', 'value'=>'Australia/ACT'),
            array('key'=>'Australia/Adelaide', 'value'=>'Australia/Adelaide'),
            array('key'=>'Australia/Brisbane', 'value'=>'Australia/Brisbane'),
            array('key'=>'Australia/Broken_Hill', 'value'=>'Australia/Broken Hill'),
            array('key'=>'Australia/Canberra', 'value'=>'Australia/Canberra'),
            array('key'=>'Australia/Currie', 'value'=>'Australia/Currie'),
            array('key'=>'Australia/Darwin', 'value'=>'Australia/Darwin'),
            array('key'=>'Australia/Eucla', 'value'=>'Australia/Eucla'),
            array('key'=>'Australia/Hobart', 'value'=>'Australia/Hobart'),
            array('key'=>'Australia/LHI', 'value'=>'Australia/LHI'),
            array('key'=>'Australia/Lindeman', 'value'=>'Australia/Lindeman'),
            array('key'=>'Australia/Lord_Howe', 'value'=>'Australia/Lord Howe'),
            array('key'=>'Australia/Melbourne', 'value'=>'Australia/Melbourne'),
            array('key'=>'Australia/North', 'value'=>'Australia/North'),
            array('key'=>'Australia/NSW', 'value'=>'Australia/NSW'),
            array('key'=>'Australia/Perth', 'value'=>'Australia/Perth'),
            array('key'=>'Australia/Queensland', 'value'=>'Australia/Queensland'),
            array('key'=>'Australia/South', 'value'=>'Australia/South'),
            array('key'=>'Australia/Sydney', 'value'=>'Australia/Sydney'),
            array('key'=>'Australia/Tasmania', 'value'=>'Australia/Tasmania'),
            array('key'=>'Australia/Victoria', 'value'=>'Australia/Victoria'),
            array('key'=>'Australia/West', 'value'=>'Australia/West'),
            array('key'=>'Australia/Yancowinna', 'value'=>'Australia/Yancowinna'),
            array('key'=>'Europe/Amsterdam', 'value'=>'Europe/Amsterdam'),
            array('key'=>'Europe/Andorra', 'value'=>'Europe/Andorra'),
            array('key'=>'Europe/Athens', 'value'=>'Europe/Athens'),
            array('key'=>'Europe/Belfast', 'value'=>'Europe/Belfast'),
            array('key'=>'Europe/Belgrade', 'value'=>'Europe/Belgrade'),
            array('key'=>'Europe/Berlin', 'value'=>'Europe/Berlin'),
            array('key'=>'Europe/Bratislava', 'value'=>'Europe/Bratislava'),
            array('key'=>'Europe/Brussels', 'value'=>'Europe/Brussels'),
            array('key'=>'Europe/Bucharest', 'value'=>'Europe/Bucharest'),
            array('key'=>'Europe/Budapest', 'value'=>'Europe/Budapest'),
            array('key'=>'Europe/Chisinau', 'value'=>'Europe/Chisinau'),
            array('key'=>'Europe/Copenhagen', 'value'=>'Europe/Copenhagen'),
            array('key'=>'Europe/Dublin', 'value'=>'Europe/Dublin'),
            array('key'=>'Europe/Gibraltar', 'value'=>'Europe/Gibraltar'),
            array('key'=>'Europe/Guernsey', 'value'=>'Europe/Guernsey'),
            array('key'=>'Europe/Helsinki', 'value'=>'Europe/Helsinki'),
            array('key'=>'Europe/Isle_of_Man', 'value'=>'Europe/Isle of Man'),
            array('key'=>'Europe/Istanbul', 'value'=>'Europe/Istanbul'),
            array('key'=>'Europe/Jersey', 'value'=>'Europe/Jersey'),
            array('key'=>'Europe/Kaliningrad', 'value'=>'Europe/Kaliningrad'),
            array('key'=>'Europe/Kiev', 'value'=>'Europe/Kiev'),
            array('key'=>'Europe/Lisbon', 'value'=>'Europe/Lisbon'),
            array('key'=>'Europe/Ljubljana', 'value'=>'Europe/Ljubljana'),
            array('key'=>'Europe/London', 'value'=>'Europe/London'),
            array('key'=>'Europe/Luxembourg', 'value'=>'Europe/Luxembourg'),
            array('key'=>'Europe/Madrid', 'value'=>'Europe/Madrid'),
            array('key'=>'Europe/Malta', 'value'=>'Europe/Malta'),
            array('key'=>'Europe/Mariehamn', 'value'=>'Europe/Mariehamn'),
            array('key'=>'Europe/Minsk', 'value'=>'Europe/Minsk'),
            array('key'=>'Europe/Monaco', 'value'=>'Europe/Monaco'),
            array('key'=>'Europe/Moscow', 'value'=>'Europe/Moscow'),
            array('key'=>'Europe/Nicosia', 'value'=>'Europe/Nicosia'),
            array('key'=>'Europe/Oslo', 'value'=>'Europe/Oslo'),
            array('key'=>'Europe/Paris', 'value'=>'Europe/Paris'),
            array('key'=>'Europe/Podgorica', 'value'=>'Europe/Podgorica'),
            array('key'=>'Europe/Prague', 'value'=>'Europe/Prague'),
            array('key'=>'Europe/Riga', 'value'=>'Europe/Riga'),
            array('key'=>'Europe/Rome', 'value'=>'Europe/Rome'),
            array('key'=>'Europe/Samara', 'value'=>'Europe/Samara'),
            array('key'=>'Europe/San_Marino', 'value'=>'Europe/San Marino'),
            array('key'=>'Europe/Sarajevo', 'value'=>'Europe/Sarajevo'),
            array('key'=>'Europe/Simferopol', 'value'=>'Europe/Simferopol'),
            array('key'=>'Europe/Skopje', 'value'=>'Europe/Skopje'),
            array('key'=>'Europe/Sofia', 'value'=>'Europe/Sofia'),
            array('key'=>'Europe/Stockholm', 'value'=>'Europe/Stockholm'),
            array('key'=>'Europe/Tallinn', 'value'=>'Europe/Tallinn'),
            array('key'=>'Europe/Tirane', 'value'=>'Europe/Tirane'),
            array('key'=>'Europe/Tiraspol', 'value'=>'Europe/Tiraspol'),
            array('key'=>'Europe/Uzhgorod', 'value'=>'Europe/Uzhgorod'),
            array('key'=>'Europe/Vaduz', 'value'=>'Europe/Vaduz'),
            array('key'=>'Europe/Vatican', 'value'=>'Europe/Vatican'),
            array('key'=>'Europe/Vienna', 'value'=>'Europe/Vienna'),
            array('key'=>'Europe/Vilnius', 'value'=>'Europe/Vilnius'),
            array('key'=>'Europe/Volgograd', 'value'=>'Europe/Volgograd'),
            array('key'=>'Europe/Warsaw', 'value'=>'Europe/Warsaw'),
            array('key'=>'Europe/Zagreb', 'value'=>'Europe/Zagreb'),
            array('key'=>'Europe/Zaporozhye', 'value'=>'Europe/Zaporozhye'),
            array('key'=>'Europe/Zurich', 'value'=>'Europe/Zurich'),
            array('key'=>'Indian/Antananarivo', 'value'=>'Indian/Antananarivo'),
            array('key'=>'Indian/Chagos', 'value'=>'Indian/Chagos'),
            array('key'=>'Indian/Christmas', 'value'=>'Indian/Christmas'),
            array('key'=>'Indian/Cocos', 'value'=>'Indian/Cocos'),
            array('key'=>'Indian/Comoro', 'value'=>'Indian/Comoro'),
            array('key'=>'Indian/Kerguelen', 'value'=>'Indian/Kerguelen'),
            array('key'=>'Indian/Mahe', 'value'=>'Indian/Mahe'),
            array('key'=>'Indian/Maldives', 'value'=>'Indian/Maldives'),
            array('key'=>'Indian/Mauritius', 'value'=>'Indian/Mauritius'),
            array('key'=>'Indian/Mayotte', 'value'=>'Indian/Mayotte'),
            array('key'=>'Indian/Reunion', 'value'=>'Indian/Reunion'),
            array('key'=>'Pacific/Apia', 'value'=>'Pacific/Apia'),
            array('key'=>'Pacific/Auckland', 'value'=>'Pacific/Auckland'),
            array('key'=>'Pacific/Chatham', 'value'=>'Pacific/Chatham'),
            array('key'=>'Pacific/Easter', 'value'=>'Pacific/Easter'),
            array('key'=>'Pacific/Efate', 'value'=>'Pacific/Efate'),
            array('key'=>'Pacific/Enderbury', 'value'=>'Pacific/Enderbury'),
            array('key'=>'Pacific/Fakaofo', 'value'=>'Pacific/Fakaofo'),
            array('key'=>'Pacific/Fiji', 'value'=>'Pacific/Fiji'),
            array('key'=>'Pacific/Funafuti', 'value'=>'Pacific/Funafuti'),
            array('key'=>'Pacific/Galapagos', 'value'=>'Pacific/Galapagos'),
            array('key'=>'Pacific/Gambier', 'value'=>'Pacific/Gambier'),
            array('key'=>'Pacific/Guadalcanal', 'value'=>'Pacific/Guadalcanal'),
            array('key'=>'Pacific/Guam', 'value'=>'Pacific/Guam'),
            array('key'=>'Pacific/Honolulu', 'value'=>'Pacific/Honolulu'),
            array('key'=>'Pacific/Johnston', 'value'=>'Pacific/Johnston'),
            array('key'=>'Pacific/Kiritimati', 'value'=>'Pacific/Kiritimati'),
            array('key'=>'Pacific/Kosrae', 'value'=>'Pacific/Kosrae'),
            array('key'=>'Pacific/Kwajalein', 'value'=>'Pacific/Kwajalein'),
            array('key'=>'Pacific/Majuro', 'value'=>'Pacific/Majuro'),
            array('key'=>'Pacific/Marquesas', 'value'=>'Pacific/Marquesas'),
            array('key'=>'Pacific/Midway', 'value'=>'Pacific/Midway'),
            array('key'=>'Pacific/Nauru', 'value'=>'Pacific/Nauru'),
            array('key'=>'Pacific/Niue', 'value'=>'Pacific/Niue'),
            array('key'=>'Pacific/Norfolk', 'value'=>'Pacific/Norfolk'),
            array('key'=>'Pacific/Noumea', 'value'=>'Pacific/Noumea'),
            array('key'=>'Pacific/Pago_Pago', 'value'=>'Pacific/Pago Pago'),
            array('key'=>'Pacific/Palau', 'value'=>'Pacific/Palau'),
            array('key'=>'Pacific/Pitcairn', 'value'=>'Pacific/Pitcairn'),
            array('key'=>'Pacific/Ponape', 'value'=>'Pacific/Ponape'),
            array('key'=>'Pacific/Port_Moresby', 'value'=>'Pacific/Port Moresby'),
            array('key'=>'Pacific/Rarotonga', 'value'=>'Pacific/Rarotonga'),
            array('key'=>'Pacific/Saipan', 'value'=>'Pacific/Saipan'),
            array('key'=>'Pacific/Samoa', 'value'=>'Pacific/Samoa'),
            array('key'=>'Pacific/Tahiti', 'value'=>'Pacific/Tahiti'),
            array('key'=>'Pacific/Tarawa', 'value'=>'Pacific/Tarawa'),
            array('key'=>'Pacific/Tongatapu', 'value'=>'Pacific/Tongatapu'),
            array('key'=>'Pacific/Truk', 'value'=>'Pacific/Truk'),
            array('key'=>'Pacific/Wake', 'value'=>'Pacific/Wake'),
            array('key'=>'Pacific/Wallis', 'value'=>'Pacific/Wallis'),
            array('key'=>'Pacific/Yap', 'value'=>'Pacific/Yap'),
        ));

        return $timezone;
    }
}


