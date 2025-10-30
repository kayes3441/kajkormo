<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = Carbon::now();

            $locations = $this->getLocations();
            $translations = $this->getTranslations();

            foreach ($locations as $divisionName => $districts) {
                // Division
                $divisionSlug = $this->makeSlug($divisionName);

                DB::table('locations')->updateOrInsert(
                    ['slug' => $divisionSlug],
                    [
                        'name' => $divisionName,
                        'country_code' => 'BD',
                        'slug' => $divisionSlug,
                        'level' => 'division',
                        'parent_id' => null,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $divisionId = DB::table('locations')->where('slug', $divisionSlug)->value('id');
                $this->addTranslationIfExists($divisionId, $divisionName, $translations);

                foreach ($districts as $districtName => $upazilas) {
                    // District slug includes division to keep unique
                    $districtSlug = $this->makeSlug($divisionName . ' ' . $districtName);

                    DB::table('locations')->updateOrInsert(
                        ['slug' => $districtSlug],
                        [
                            'name' => $districtName,
                            'country_code' => 'BD',
                            'slug' => $districtSlug,
                            'level' => 'district',
                            'parent_id' => $divisionId,
                            'status' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );

                    $districtId = DB::table('locations')->where('slug', $districtSlug)->value('id');
                    $this->addTranslationIfExists($districtId, $districtName, $translations);

                    foreach ($upazilas as $upazilaName) {
                        // Sub-district slug uses division + district + upazila (guarantees uniqueness)
                        $subSlug = $this->makeSlug($divisionName . ' ' . $districtName . ' ' . $upazilaName);

                        DB::table('locations')->updateOrInsert(
                            ['slug' => $subSlug],
                            [
                                'name' => $upazilaName,
                                'country_code' => 'BD',
                                'slug' => $subSlug,
                                'level' => 'sub-district',
                                'parent_id' => $districtId,
                                'status' => 1,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        );

                        $subId = DB::table('locations')->where('slug', $subSlug)->value('id');
                        $this->addTranslationIfExists($subId, $upazilaName, $translations);
                    }
                }
            }
        });
    }

    private function makeSlug(string $name): string
    {
        // normalize to ascii-friendly slug
        return Str::slug($name, '-');
    }

    private function addTranslationIfExists(int $locationId, string $name, array $translations): void
    {
        if (! isset($translations[$name])) {
            return;
        }

        DB::table('translations')->updateOrInsert(
            [
                'translatable_type' => 'App\\Models\\Location',
                'translatable_id' => $locationId,
                'locale' => 'bn',
                'key' => 'name',
            ],
            [
                'value' => $translations[$name],
            ]
        );
    }

    private function getLocations(): array
    {
        return [

            // -----------------------
            // Chattogram Division
            // -----------------------
            'Chattogram Division' => [
                'Cumilla' => [
                    'Debidwar','Barura','Brahmanpara','Chandina','Chauddagram','Daudkandi','Homna','Laksam','Muradnagar','Nangalkot','Sadar','Meghna','Monohargonj','Sadarsouth','Titas','Burichang','Lalmai'
                ],
                'Feni' => ['Chhagalnaiya','Sadar','Sonagazi','Fulgazi','Parshuram','Daganbhuiyan'],
                'Brahmanbaria' => ['Sadar','Kasba','Nasirnagar','Sarail','Ashuganj','Akhaura','Nabinagar','Bancharampur','Bijoynagar'],
                'Rangamati' => ['Sadar','Kaptai','Kawkhali','Baghaichari','Barkal','Langadu','Rajasthali','Belaichari','Juraichari','Naniarchar'],
                'Noakhali' => ['Sadar','Companiganj','Begumganj','Hatia','Subarnachar','Kabirhat','Senbug','Chatkhil','Sonaimuri'],
                'Chandpur' => ['Haimchar','Kachua','Shahrasti','Sadar','Matlabsouth','Hajiganj','Matlabnorth','Faridgonj'],
                'Lakshmipur' => ['Sadar','Kamalnagar','Raipur','Ramgati','Ramganj'],
                'Chattogram' => [
                    'Agrabad','Halishahar','Chawkbazar','Patenga','Kotwali','Nasirabad','Chandgaon','Hathazari','Muradpur','Khulshi',
                    'Oxygen','Cornelhat','Baizid','Bohoddarhat','Pahartali','Alankar','Bakoliya','Bandar','New Market','Karnafuly',
                    'Patiya','Mirsharai','Panchlaish','Raozan','Lalkhan Bazar','CDA Avenue','Fatikchari','Anderkilla','Boalkhali',
                    'Kajir Dewry','Jamalkhan','Satkania','Rangunia','Anwara','Chandanaish','Sholashahar','Lohagara','Banskhali','Sandwip'
                ],
                'Coxsbazar' => ['Sadar','Chakaria','Kutubdia','Ukhiya','Moheshkhali','Pekua','Ramu','Teknaf'],
                'Khagrachhari' => ['Sadar','Dighinala','Panchari','Laxmichhari','Mohalchari','Manikchari','Ramgarh','Matiranga','Guimara'],
                'Bandarban' => ['Sadar','Alikadam','Naikhongchhari','Rowangchhari','Lama','Ruma','Thanchi'],
            ],

            // -----------------------
            // Rajshahi Division
            // -----------------------
            'Rajshahi Division' => [
                'Sirajganj' => ['Belkuchi','Chauhali','Kamarkhand','Kazipur','Raigonj','Shahjadpur','Sirajganjsadar','Tarash','Ullapara'],
                'Pabna' => ['Sujanagar','Ishurdi','Bhangura','Pabnasadar','Bera','Atghoria','Chatmohar','Santhia','Faridpur'],
                'Bogura' => ['Kahaloo','Sadar','Shariakandi','Shajahanpur','Dupchanchia','Adamdighi','Nondigram','Sonatala','Dhunot','Gabtali','Sherpur','Shibganj'],
                'Rajshahi' => ['Paba','Durgapur','Mohonpur','Charghat','Puthia','Bagha','Godagari','Tanore','Bagmara'],
                'Natore' => ['Natoresadar','Singra','Baraigram','Bagatipara','Lalpur','Gurudaspur','Naldanga'],
                'Joypurhat' => ['Akkelpur','Kalai','Khetlal','Panchbibi','Joypurhatsadar'],
                'Chapainawabganj' => ['Chapainawabganjsadar','Gomostapur','Nachol','Bholahat','Shibganj'],
                'Naogaon' => ['Mohadevpur','Badalgachi','Patnitala','Dhamoirhat','Niamatpur','Manda','Atrai','Raninagar','Naogaonsadar','Porsha','Sapahar'],
            ],

            // -----------------------
            // Khulna Division
            // -----------------------
            'Khulna Division' => [
                'Jashore' => ['Manirampur','Abhaynagar','Bagherpara','Chougachha','Jhikargacha','Keshabpur','Sadar','Sharsha'],
                'Satkhira' => ['Assasuni','Debhata','Kalaroa','Satkhirasadar','Shyamnagar','Tala','Kaliganj'],
                'Meherpur' => ['Mujibnagar','Meherpursadar','Gangni'],
                'Narail' => ['Narailsadar','Lohagara','Kalia'],
                'Chuadanga' => ['Chuadangasadar','Alamdanga','Damurhuda','Jibannagar'],
                'Kushtia' => ['Kushtiasadar','Kumarkhali','Khoksa','Mirpur','Daulatpur','Bheramara'],
                'Magura' => ['Shalikha','Sreepur','Magurasadar','Mohammadpur'],
                'Khulna' => ['Paikgasa','Fultola','Digholia','Rupsha','Terokhada','Dumuria','Botiaghata','Dakop','Koyra'],
                'Bagerhat' => ['Fakirhat','Sadar','Mollahat','Sarankhola','Rampal','Morrelganj','Kachua','Mongla','Chitalmari'],
                'Jhenaidah' => ['Sadar','Shailkupa','Harinakundu','Kaliganj','Kotchandpur','Moheshpur'],
            ],

            // -----------------------
            // Barishal Division
            // -----------------------
            'Barishal Division' => [
                'Jhalakathi' => ['Sadar','Kathalia','Nalchity','Rajapur'],
                'Patuakhali' => ['Bauphal','Sadar','Dumki','Dashmina','Kalapara','Mirzaganj','Galachipa','Rangabali'],
                'Pirojpur' => ['Sadar','Nazirpur','Kawkhali','Bhandaria','Mathbaria','Nesarabad','Indurkani'],
                'Barishal' => ['Barishalsadar','Bakerganj','Babuganj','Wazirpur','Banaripara','Gournadi','Agailjhara','Mehendiganj','Muladi','Hizla'],
                'Bhola' => ['Sadar','Borhanuddin','Charfesson','Doulatkhan','Monpura','Tazumuddin','Lalmohan'],
                'Barguna' => ['Amtali','Sadar','Betagi','Bamna','Pathorghata','Taltali'],
            ],

            // -----------------------
            // Sylhet Division
            // -----------------------
            'Sylhet Division' => [
                'Sylhet' => ['Balaganj','Beanibazar','Bishwanath','Companiganj','Fenchuganj','Golapganj','Gowainghat','Jaintiapur','Kanaighat','Sylhetsadar','Zakiganj','Dakshinsurma','Osmaninagar'],
                'Moulvibazar' => ['Barlekha','Kamolganj','Kulaura','Moulvibazarsadar','Rajnagar','Sreemangal','Juri'],
                'Habiganj' => ['Nabiganj','Bahubal','Ajmiriganj','Baniachong','Lakhai','Chunarughat','Habiganjsadar','Madhabpur','Shayestaganj'],
                'Sunamganj' => ['Sadar','Southsunamganj','Bishwambarpur','Chhatak','Jagannathpur','Dowarabazar','Tahirpur','Dharmapasha','Jamalganj','Shalla','Derai','Madhyanagar'],
            ],

            // -----------------------
            // Dhaka Division
            // -----------------------
            'Dhaka Division' => [
                'Narsingdi' => ['Belabo','Monohardi','Narsingdisadar','Palash','Raipura','Shibpur'],
                'Gazipur' => ['Kaliganj','Kaliakair','Kapasia','Sadar','Sreepur'],
                'Shariatpur' => ['Sadar','Naria','Zajira','Gosairhat','Bhedarganj','Damudya'],
                'Narayanganj' => ['Araihazar','Bandar','Narayanganjsadar','Rupganj','Sonargaon'],
                'Tangail' => ['Basail','Bhuapur','Delduar','Ghatail','Gopalpur','Madhupur','Mirzapur','Nagarpur','Sakhipur','Tangailsadar','Kalihati','Dhanbari'],
                'Kishoreganj' => ['Itna','Katiadi','Bhairab','Tarail','Hossainpur','Pakundia','Kuliarchar','Kishoreganjsadar','Karimgonj','Bajitpur','Austagram','Mithamoin','Nikli'],
                'Manikganj' => ['Harirampur','Saturia','Sadar','Gior','Shibaloy','Doulatpur','Singiar'],
                'Dhaka' => ['Uttara','Mirpur','Mohammadpur','Gulshan','Savar','Jatrabari','Badda','Bashundhara','Dhanmondi','Sutrapur','Tongi','Khilgaon','Cantonment','Basabo','Keraniganj','Baridhara','Rampura','Tejgaon','Paltan','Lalbag','Wari','Demra','Khilkhet','Mogbazar','Kamrangirchar','Banasree','Motijheel','Hazaribagh','Banani','Mohakhali','Bangshal','Elephant Road','Malibag','Aftab nagar','Kafrul','Chaukbazar','Dhamrai','Farmgate','ECB Chattar','Kamalapur','60 Feet Road','Banglamotor','Shyamoli','New Market','Vatara','Eskaton','Lalmatia','Ramna','Kotwali','Shantinagar','Purbachal','Nawabganj','Bosila','Motalib Plaza','Shewrapara','Kalabagan','Panthapath'],
                'Munshiganj' => ['Sadar','Sreenagar','Sirajdikhan','Louhajanj','Gajaria','Tongibari'],
                'Rajbari' => ['Sadar','Goalanda','Pangsa','Baliakandi','Kalukhali'],
                'Madaripur' => ['Sadar','Shibchar','Kalkini','Rajoir','Dasar'],
                'Gopalganj' => ['Sadar','Kashiani','Tungipara','Kotalipara','Muksudpur'],
                'Faridpur' => ['Sadar','Alfadanga','Boalmari','Sadarpur','Nagarkanda','Bhanga','Charbhadrasan','Madhukhali','Saltha'],
            ],

            // -----------------------
            // Rangpur Division
            // -----------------------
            'Rangpur Division' => [
                'Panchagarh' => ['Panchagarhsadar','Debiganj','Boda','Atwari','Tetulia'],
                'Dinajpur' => ['Nawabganj','Birganj','Ghoraghat','Birampur','Parbatipur','Bochaganj','Kaharol','Fulbari','Dinajpursadar','Hakimpur','Khansama','Birol','Chirirbandar'],
                'Lalmonirhat' => ['Sadar','Kaliganj','Hatibandha','Patgram','Aditmari'],
                'Nilphamari' => ['Syedpur','Domar','Dimla','Jaldhaka','Kishorganj','Nilphamarisadar'],
                'Gaibandha' => ['Sadullapur','Gaibandhasadar','Palashbari','Saghata','Gobindaganj','Sundarganj','Phulchari'],
                'Thakurgaon' => ['Thakurgaonsadar','Pirganj','Ranisankail','Haripur','Baliadangi'],
                'Rangpur' => ['Rangpursadar','Gangachara','Taragonj','Badargonj','Mithapukur','Pirgonj','Kaunia','Pirgacha'],
                'Kurigram' => ['Kurigramsadar','Nageshwari','Bhurungamari','Phulbari','Rajarhat','Ulipur','Chilmari','Rowmari','Charrajibpur'],
            ],

            // -----------------------
            // Mymensingh Division
            // -----------------------
            'Mymensingh Division' => [
                'Sherpur' => ['Sherpursadar','Nalitabari','Sreebordi','Nokla','Jhenaigati'],
                'Mymensingh' => ['Fulbaria','Trishal','Bhaluka','Muktagacha','Mymensinghsadar','Dhobaura','Phulpur','Haluaghat','Gouripur','Gafargaon','Iswarganj','Nandail','Tarakanda'],
                'Jamalpur' => ['Jamalpursadar','Melandah','Islampur','Dewangonj','Sarishabari','Madarganj','Bokshiganj'],
                'Netrokona' => ['Barhatta','Durgapur','Kendua','Atpara','Madan','Khaliajuri','Kalmakanda','Mohongonj','Purbadhala','Netrokonasadar'],
            ],
        ];
    }

    private function getTranslations(): array
    {
        // Translations from the Bangla block you supplied. Add more key=>value pairs if you want full coverage.
        return [
            // Divisions
            'Chattogram Division' => 'চট্টগ্রাম বিভাগ',
            'Rajshahi Division' => 'রাজশাহী বিভাগ',
            'Khulna Division' => 'খুলনা বিভাগ',
            'Barishal Division' => 'বরিশাল বিভাগ',
            'Sylhet Division' => 'সিলেট বিভাগ',
            'Dhaka Division' => 'ঢাকা বিভাগ',
            'Rangpur Division' => 'রংপুর বিভাগ',
            'Mymensingh Division' => 'ময়মনসিংহ বিভাগ',

            // Selected districts and obvious matches (expand as needed)
            'Cumilla' => 'কুমিল্লা',
            'Feni' => 'ফেনী',
            'Brahmanbaria' => 'ব্রাহ্মণবাড়িয়া',
            'Rangamati' => 'রাঙ্গামাটি',
            'Noakhali' => 'নোয়াখালী',
            'Chandpur' => 'চাঁদপুর',
            'Lakshmipur' => 'লক্ষ্মীপুর',
            'Chattogram' => 'চট্টগ্রাম',
            'Coxsbazar' => 'কক্সবাজার',
            'Khagrachhari' => 'খাগড়াছড়ি',
            'Bandarban' => 'বান্দরবান',

            'Sirajganj' => 'সিরাজগঞ্জ',
            'Pabna' => 'পাবনা',
            'Bogura' => 'বগুড়া',
            'Rajshahi' => 'রাজশাহী',
            'Natore' => 'নাটোর',
            'Joypurhat' => 'জয়পুরহাট',
            'Chapainawabganj' => 'চাঁপাইনবাবগঞ্জ',
            'Naogaon' => 'নওগাঁ',

            'Jashore' => 'যশোর',
            'Satkhira' => 'সাতক্ষীরা',
            'Meherpur' => 'মেহেরপুর',
            'Narail' => 'নড়াইল',
            'Chuadanga' => 'চুয়াডাঙ্গা',
            'Kushtia' => 'কুষ্টিয়া',
            'Magura' => 'মাগুরা',
            'Khulna' => 'খুলনা',
            'Bagerhat' => 'বাগেরহাট',
            'Jhenaidah' => 'ঝিনাইদহ',

            'Jhalakathi' => 'ঝালকাঠি',
            'Patuakhali' => 'পটুয়াখালী',
            'Pirojpur' => 'পিরোজপুর',
            'Barishal' => 'বরিশাল',
            'Bhola' => 'ভোলা',
            'Barguna' => 'বরগুনা',

            'Sylhet' => 'সিলেট',
            'Moulvibazar' => 'মৌলভীবাজার',
            'Habiganj' => 'হবিগঞ্জ',
            'Sunamganj' => 'সুনামগঞ্জ',

            'Narsingdi' => 'নরসিংদী',
            'Gazipur' => 'গাজীপুর',
            'Shariatpur' => 'শরীয়তপুর',
            'Narayanganj' => 'নারায়ণগঞ্জ',
            'Tangail' => 'টাঙ্গাইল',
            'Kishoreganj' => 'কিশোরগঞ্জ',
            'Manikganj' => 'মানিকগঞ্জ',
            'Dhaka' => 'ঢাকা',
            'Munshiganj' => 'মুন্সিগঞ্জ',
            'Rajbari' => 'রাজবাড়ী',
            'Madaripur' => 'মাদারীপুর',
            'Gopalganj' => 'গোপালগঞ্জ',
            'Faridpur' => 'ফরিদপুর',

            'Panchagarh' => 'পঞ্চগড়',
            'Dinajpur' => 'দিনাজপুর',
            'Lalmonirhat' => 'লালমনিরহাট',
            'Nilphamari' => 'নীলফামারী',
            'Gaibandha' => 'গাইবান্ধা',
            'Thakurgaon' => 'ঠাকুরগাঁও',
            'Rangpur' => 'রংপুর',
            'Kurigram' => 'কুড়িগ্রাম',

            'Sherpur' => 'শেরপুর',
            'Mymensingh' => 'ময়মনসিংহ',
            'Jamalpur' => 'জামালপুর',
            'Netrokona' => 'নেত্রকোণা',

            // Generic common names (so translations for 'Sadar' etc. will be used)
            'Sadar' => 'সদর',
            'Sadar South' => 'সদর দক্ষিণ',
            'Natoresadar' => 'নাটোর সদর',
            'Pabnasadar' => 'পাবনা সদর',
            'Barishalsadar' => 'বরিশাল সদর',
            'Moulvibazarsadar' => 'মৌলভীবাজার সদর',
            'Joypurhatsadar' => 'জয়পুরহাট সদর',
            'Naogaonsadar' => 'নওগাঁ সদর',
            'Kushtiasadar' => 'কুষ্টিয়া সদর',
            'Satkhirasadar' => 'সাতক্ষীরা সদর',
            'Patuakhali Sadar' => 'পটুয়াখালী সদর',
            'Bhola Sadar' => 'ভোলা সদর',
            'Narayanganjsadar' => 'নারায়ণগঞ্জ সদর',
            'Tangailsadar' => 'টাঙ্গাইল সদর',
            'Dinajpursadar' => 'দিনাজপুর সদর',
            'Rangpursadar' => 'রংপুর সদর',
            'Kurigramsadar' => 'কুড়িগ্রাম সদর',
            'Sherpursadar' => 'শেরপুর সদর',
            'Mymensinghsadar' => 'ময়মনসিংহ সদর',
            'Netrokonasadar' => 'নেত্রকোণা সদর',
        ];
    }
}
