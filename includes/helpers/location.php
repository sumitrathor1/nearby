<?php
declare(strict_types=1);

/**
 * Return curated static location data verified by seniors around MITS Gwalior.
 */
function nearby_get_verified_locations(): array
{
    return [
        [
            'id' => 1,
            'name' => 'MITS College (Main Campus)',
            'type' => 'college',
            'lat' => 26.2325,
            'lng' => 78.2053,
            'description' => 'Madhav Institute of Technology & Science. The heart of the campus.',
            'verified_by' => 'Admin'
        ],
        [
            'id' => 2,
            'name' => 'Student Villa PG (Gole ka Mandir)',
            'type' => 'housing',
            'lat' => 26.2380,
            'lng' => 78.2090,
            'price' => '₹6,500/mo',
            'description' => 'Single & Double sharing, near Gole ka Mandir circle. Food included.',
            'verified_by' => 'Amit (Civil, 3rd Year)'
        ],
        [
            'id' => 3,
            'name' => 'Sai Boys Hostel (Morar)',
            'type' => 'housing',
            'lat' => 26.2280,
            'lng' => 78.2150,
            'price' => '₹5,000/mo',
            'description' => 'Budget friendly, walking distance to market.',
            'verified_by' => 'Rohit (Electrical, 2nd Year)'
        ],
        [
            'id' => 4,
            'name' => 'Maa Annapurna Mess (Pinto Park)',
            'type' => 'food',
            'lat' => 26.2350,
            'lng' => 78.2020,
            'price' => '₹60/thali',
            'description' => 'Famous for home-like Dal Bati and Roti. Monthly pass available.',
            'verified_by' => 'Priya (CS, Final Year)'
        ],
        [
            'id' => 5,
            'name' => 'City Centre Library',
            'type' => 'essential',
            'lat' => 26.2150,
            'lng' => 78.1950,
            'description' => 'Public library with AC, good for exam prep. 3km from college.',
            'verified_by' => 'Sandeep (Alumni)'
        ],
        [
            'id' => 6,
            'name' => 'Girls Residency (Residency Rd)',
            'type' => 'housing',
            'lat' => 26.2250,
            'lng' => 78.2000,
            'price' => '₹8,000/mo',
            'description' => 'Secure society, 24/7 guard, near Residency.',
            'verified_by' => 'Neha (Architecture, 3rd Year)'
        ],
        [
            'id' => 7,
            'name' => 'Sun Temple (Surya Mandir)',
            'type' => 'essential',
            'lat' => 26.2370,
            'lng' => 78.2120,
            'description' => 'Peaceful place for morning walks and meditation.',
            'verified_by' => 'Community'
        ],
        [
            'id' => 8,
            'name' => 'Kalidevi Mess (Padav)',
            'type' => 'food',
            'lat' => 26.2180,
            'lng' => 78.1900,
            'price' => '₹70/thali',
            'description' => 'Spicy and tasty food, open late. Popular for dinner.',
            'verified_by' => 'Rohan (Civil, 2nd Year)'
        ],
        [
            'id' => 9,
            'name' => 'Maa Kali Mess (Morar)',
            'type' => 'food',
            'lat' => 26.2290,
            'lng' => 78.2160,
            'price' => '₹65/thali',
            'description' => 'Good quality tiffin service available.',
            'verified_by' => 'Ankit (EE, 3rd Year)'
        ]
        [
    'id' => 10,
    'name' => 'Gole Ka Mandir Bus Stop',
    'type' => 'essential',
    'lat' => 26.2395,
    'lng' => 78.2085,
    'description' => 'Major transport hub connecting city routes.',
    'verified_by' => 'Community'
],
[
    'id' => 11,
    'name' => 'Campus Stationery & Print Shop',
    'type' => 'essential',
    'lat' => 26.2334,
    'lng' => 78.2068,
    'description' => 'Printouts, project reports, notebooks and supplies.',
    'verified_by' => 'Rahul (Mechanical, 2nd Year)'
],
[
    'id' => 12,
    'name' => 'Chai Sutta Cafe',
    'type' => 'food',
    'lat' => 26.2363,
    'lng' => 78.2075,
    'price' => '₹120',
    'description' => 'Popular evening hangout for tea, snacks and study breaks.',
    'verified_by' => 'Harsh (IT, 3rd Year)'
],
[
    'id' => 13,
    'name' => 'Student Study Library',
    'type' => 'essential',
    'lat' => 26.2310,
    'lng' => 78.2030,
    'price' => '₹300/mo',
    'description' => 'Quiet AC library with WiFi for exam preparation.',
    'verified_by' => 'Community'
],
[
    'id' => 14,
    'name' => 'Fresh Milk Booth',
    'type' => 'food',
    'lat' => 26.2348,
    'lng' => 78.2045,
    'price' => '₹60/litre',
    'description' => 'Daily fresh milk and dairy products.',
    'verified_by' => 'Local Vendor'
],
[
    'id' => 15,
    'name' => 'MITS Sports Ground',
    'type' => 'college',
    'lat' => 26.2337,
    'lng' => 78.2025,
    'description' => 'Popular spot for cricket and football practice.',
    'verified_by' => 'Admin'
]
    ];
}

/**
 * Return a normalised integer price from formatted strings like ₹6,500/mo.
 */
function nearby_extract_price_value(?string $price): ?int
{
    if ($price === null || $price === '') {
        return null;
    }

    $digits = preg_replace('/[^0-9]/', '', $price);
    if ($digits === null || $digits === '') {
        return null;
    }

    return (int) $digits;
}

/**
 * Filter locations by optional category, price, and search query.
 */
function nearby_filter_locations(array $locations, array $criteria = []): array
{
    $type = $criteria['type'] ?? null;
    $maxPrice = isset($criteria['max_price']) ? (int) $criteria['max_price'] : null;
    $search = isset($criteria['q']) ? trim((string) $criteria['q']) : null;

    return array_values(array_filter($locations, static function (array $location) use ($type, $maxPrice, $search): bool {
        if ($type && ($location['type'] ?? '') !== $type) {
            return false;
        }

        if ($maxPrice !== null) {
            $priceValue = nearby_extract_price_value($location['price'] ?? null);
            if ($priceValue !== null && $priceValue > $maxPrice) {
                return false;
            }
        }

        if ($search !== null && $search !== '') {
            $haystack = strtolower(($location['name'] ?? '') . ' ' . ($location['description'] ?? '') . ' ' . ($location['verified_by'] ?? ''));
            if (strpos($haystack, strtolower($search)) === false) {
                return false;
            }
        }

        return true;
    }));
}

/**
 * Build a trimmed payload ready for JSON responses.
 */
function nearby_serialize_locations(array $locations): array
{
    return array_map(static function (array $location): array {
        return [
            'id' => $location['id'],
            'name' => $location['name'],
            'type' => $location['type'],
            'lat' => $location['lat'],
            'lng' => $location['lng'],
            'price' => $location['price'] ?? null,
            'description' => $location['description'],
            'verified_by' => $location['verified_by']
        ];
    }, $locations);
}
