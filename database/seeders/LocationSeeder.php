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

            // Chattogram Districts
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

            // Cumilla Upazilas
            'Debidwar' => 'দেবিদ্বার',
            'Barura' => 'বরুড়া',
            'Brahmanpara' => 'ব্রাহ্মণপাড়া',
            'Chandina' => 'চান্দিনা',
            'Chauddagram' => 'চৌদ্দগ্রাম',
            'Daudkandi' => 'দাউদকান্দি',
            'Homna' => 'হোমনা',
            'Laksam' => 'লাকসাম',
            'Muradnagar' => 'মুরাদনগর',
            'Nangalkot' => 'নাঙ্গলকোট',
            'Meghna' => 'মেঘনা',
            'Monohargonj' => 'মনোহরগঞ্জ',
            'Sadar South' => 'সদর দক্ষিণ',
            'Titas' => 'তিতাস',
            'Burichang' => 'বুড়িচং',
            'Lalmai' => 'লালমাই',

            // Feni Upazilas
            'Chhagalnaiya' => 'ছাগলনাইয়া',
            'Sadar' => 'সদর',
            'Sonagazi' => 'সোনাগাজী',
            'Fulgazi' => 'ফুলগাজী',
            'Parshuram' => 'পরশুরাম',
            'Daganbhuiyan' => 'দাগনভূঞা',

            // Brahmanbaria Upazilas
            'Brahmanbaria Sadar' => 'ব্রাহ্মণবাড়িয়া সদর',
            'Kasba' => 'কসবা',
            'Nasirnagar' => 'নাসিরনগর',
            'Sarail' => 'সরাইল',
            'Ashuganj' => 'আশুগঞ্জ',
            'Akhaura' => 'আখাউড়া',
            'Nabinagar' => 'নবীনগর',
            'Bancharampur' => 'বাঞ্ছারামপুর',
            'Bijoynagar' => 'বিজয়নগর',

            // Rangamati Upazilas
            'Rangamati Sadar' => 'রাঙ্গামাটি সদর',
            'Kaptai' => 'কাপ্তাই',
            'Kawkhali' => 'কাউখালী',
            'Baghaichari' => 'বাঘাইছড়ি',
            'Barkal' => 'বরকল',
            'Langadu' => 'লংগদু',
            'Rajasthali' => 'রাজস্থলী',
            'Belaichari' => 'বিলাইছড়ি',
            'Juraichari' => 'জুরাছড়ি',
            'Naniarchar' => 'নানিয়ারচর',

            // Noakhali Upazilas
            'Noakhali Sadar' => 'নোয়াখালী',
            'Companiganj' => 'কোম্পানীগঞ্জ',
            'Begumganj' => 'বেগমগঞ্জ',
            'Hatia' => 'হাতিয়া',
            'Subarnachar' => 'সুবর্ণচর',
            'Kabirhat' => 'কবিরহাট',
            'Senbagh' => 'সেনবাগ',
            'Chatkhil' => 'চাটখিল',
            'Sonaimuri' => 'সোনাইমুড়ী',

            // Chandpur Upazilas
            'Haimchar' => 'হাইমচর',
            'Kachua' => 'কচুয়া',
            'Shahrasti' => 'শাহরাস্তি',
            'Chandpur Sadar' => 'চাঁদপুর সদর',
            'Matlabsouth' => 'মতলব',
            'Hajiganj' => 'হাজীগঞ্জ',
            'Matlabnorth' => 'মতলব',
            'Faridgonj' => 'ফরিদগঞ্জ',

            // Lakshmipur Upazilas
            'Lakshmipur Sadar' => 'লক্ষ্মীপুর সদর',
            'Kamalnagar' => 'কমলনগর',
            'Raipur' => 'রায়পুর',
            'Ramgati' => 'রামগতি',
            'Ramganj' => 'রামগঞ্জ',

            // Chattogram City Upazilas
            'Agrabad' => 'আগ্রাবাদ',
            'Halishahar' => 'হালিশহর',
            'Chawkbazar' => 'চকবাজার',
            'Patenga' => 'পতেঙ্গা',
            'Kotwali' => 'কতোয়ালি',
            'Nasirabad' => 'নাসিরাবাদ',
            'Chandgaon' => 'চান্দগাঁও',
            'Hathazari' => 'হাটহাজারি',
            'Muradpur' => 'মুরাদপুর',
            'Khulshi' => 'খুলশি',
            'Oxygen' => 'অক্সিজেন',
            'Cornelhat' => 'কর্ণেলহাট',
            'Baizid' => 'বায়জিদ',
            'Bohoddarhat' => 'বদ্দারহাট',
            'Pahartali' => 'পাহারতলী',
            'Alankar' => 'অলঙ্গকার',
            'Bakoliya' => 'বাকলিয়া',
            'Bandar' => 'বন্দর',
            'New Market' => 'নিউ মার্কেট',
            'Karnafuly' => 'কর্ণফুলি',
            'Patiya' => 'পটিয়া',
            'Mirsharai' => 'মিরশরাই',
            'Panchlaish' => 'পান্সলাইশ',
            'Raozan' => 'রাওজান',
            'Lalkhan Bazar' => 'লালখান বাজার',
            'CDA Avenue' => 'সিডিএ এভেনিউ',
            'Fatikchari' => 'ফটিকছড়ি',
            'Anderkilla' => 'আন্দরকিল্লা',
            'Boalkhali' => 'বোয়ালখালী',
            'Kajir Dewry' => 'কাজির দেওরি',
            'Jamalkhan' => 'জামাল খান',
            'Satkania' => 'সাতকানিয়া',
            'Rangunia' => 'রাঙ্গুনিয়া',
            'Anwara' => 'আনোয়ারা',
            'Chandanaish' => 'চন্দনাইশ',
            'Sholashahar' => 'ষোল শহর',
            'Lohagara' => 'লোহাগাড়া',
            'Banskhali' => 'বাঁশখালী',
            'Sandwip' => 'সন্দীপ',

            // Cox's Bazar Upazilas
            'Coxsbazar Sadar' => 'কক্সবাজার সদর',
            'Chakaria' => 'চকরিয়া',
            'Kutubdia' => 'কুতুবদিয়া',
            'Ukhiya' => 'উখিয়া',
            'Moheshkhali' => 'মহেশখালী',
            'Pekua' => 'পেকুয়া',
            'Ramu' => 'রামু',
            'Teknaf' => 'টেকনাফ',

            // Khagrachhari Upazilas
            'Khagrachhari Sadar' => 'খাগড়াছড়ি সদর',
            'Dighinala' => 'দিঘীনালা',
            'Panchari' => 'পানছড়ি',
            'Laxmichhari' => 'লক্ষীছড়ি',
            'Mohalchari' => 'মহালছড়ি',
            'Manikchari' => 'মানিকছড়ি',
            'Ramgarh' => 'রামগড়',
            'Matiranga' => 'মাটিরাঙ্গা',
            'Guimara' => 'গুইমারা',

            // Bandarban Upazilas
            'Bandarban Sadar' => 'বান্দরবান সদর',
            'Alikadam' => 'আলীকদম',
            'Naikhongchhari' => 'নাইক্ষ্যংছড়ি',
            'Rowangchhari' => 'রোয়াংছড়ি',
            'Lama' => 'লামা',
            'Ruma' => 'রুমা',
            'Thanchi' => 'থানচি',
        ];
    }

}
