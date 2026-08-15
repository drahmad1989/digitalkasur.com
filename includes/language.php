<?php
/**
 * Language System - DigitalKasur.com
 * English + Roman Urdu Support
 */

// Get current language from session or default
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULT_LANG;

// If language changed via URL
if (isset($_GET['lang']) && in_array($_GET['lang'], array_keys(SUPPORTED_LANGS))) {
    $_SESSION['lang'] = $_GET['lang'];
    $current_lang = $_GET['lang'];
}

// Translation Arrays
$translations = [

    // Navigation
    'nav_home' => ['en' => 'Home', 'ur' => 'Home'],
    'nav_events' => ['en' => 'Event Services', 'ur' => 'Event Services'],
    'nav_digital' => ['en' => 'Digital Services', 'ur' => 'Digital Services'],
    'nav_business' => ['en' => 'Business Directory', 'ur' => 'Business Directory'],
    'nav_jobs' => ['en' => 'Jobs', 'ur' => 'Jobs'],
    'nav_news' => ['en' => 'News', 'ur' => 'News'],
    'nav_cities' => ['en' => 'Our Cities', 'ur' => 'Humari Shehri'],
    'nav_blog' => ['en' => 'Blog', 'ur' => 'Blog'],
    'nav_contact' => ['en' => 'Contact', 'ur' => 'Contact'],
    'nav_login' => ['en' => 'Login', 'ur' => 'Login'],
    'nav_register' => ['en' => 'Register', 'ur' => 'Register'],
    'nav_search' => ['en' => 'Search...', 'ur' => 'Search karein...'],

    // Hero Section
    'hero_title' => ['en' => 'Event Management & Digital Services in Kasur District', 'ur' => 'Kasur District mein Event Management aur Digital Services'],
    'hero_subtitle' => ['en' => 'Your trusted partner for weddings, corporate events, web development, SEO, and all digital services. Serving Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan, and Theng More.', 'ur' => 'Weddings, corporate events, web development, SEO, aur tamam digital services ke liye aapka bharosemand partner. Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan, aur Theng More ki khidmat mein.'],
    'hero_btn_events' => ['en' => 'Explore Events', 'ur' => 'Events Dekhein'],
    'hero_btn_digital' => ['en' => 'Digital Services', 'ur' => 'Digital Services'],

    // Cities Section
    'cities_title' => ['en' => 'Cities We Serve', 'ur' => 'Humari Shehri'],
    'cities_subtitle' => ['en' => 'Providing top-quality services across Kasur District', 'ur' => 'Kasur District bhar mein behtareen khidmaat'],

    // Event Services
    'events_title' => ['en' => 'Event Management Services', 'ur' => 'Event Management Services'],
    'events_subtitle' => ['en' => 'Make your events memorable with our professional services', 'ur' => 'Apne events ko yaadgar banayein humari professional services se'],
    'events_wedding' => ['en' => 'Wedding Events', 'ur' => 'Wedding Events'],
    'events_wedding_desc' => ['en' => 'Complete wedding planning from Mehndi to Walima', 'ur' => 'Mehndi se Walima tak complete wedding planning'],
    'events_birthday' => ['en' => 'Birthday Parties', 'ur' => 'Birthday Parties'],
    'events_birthday_desc' => ['en' => 'Theme parties, cakes, decorations & entertainment', 'ur' => 'Theme parties, cakes, decorations aur entertainment'],
    'events_corporate' => ['en' => 'Corporate Events', 'ur' => 'Corporate Events'],
    'events_corporate_desc' => ['en' => 'Meetings, conferences, seminars & workshops', 'ur' => 'Meetings, conferences, seminars aur workshops'],
    'events_festival' => ['en' => 'Festivals & Melas', 'ur' => 'Festivals aur Melas'],
    'events_festival_desc' => ['en' => 'Cultural festivals, melas & community events', 'ur' => 'Cultural festivals, melas aur community events'],
    'events_seminar' => ['en' => 'Seminars & Conferences', 'ur' => 'Seminars aur Conferences'],
    'events_concert' => ['en' => 'Concerts & Shows', 'ur' => 'Concerts aur Shows'],
    'events_ceremony' => ['en' => 'Ceremonial Functions', 'ur' => 'Ceremonial Functions'],
    'events_rally' => ['en' => 'Political Rallies', 'ur' => 'Political Rallies'],
    'events_view_all' => ['en' => 'View All Event Services', 'ur' => 'Tamam Event Services Dekhein'],
    'events_planning' => ['en' => 'Planning an Event?', 'ur' => 'Event plan kar rahe hain?'],
    'events_cta' => ['en' => 'Let us help you create unforgettable memories. Contact us for professional event management services.', 'ur' => 'Hum aapko yaadgar yaadein banane mein madad karte hain. Professional event management services ke liye hum se contact karein.'],
    'events_get_quote' => ['en' => 'Get a Quote', 'ur' => 'Quote Lein'],

    // Digital Services
    'digital_title' => ['en' => 'Digital Services', 'ur' => 'Digital Services'],
    'digital_subtitle' => ['en' => 'Grow your business with our comprehensive digital solutions', 'ur' => 'Hamare comprehensive digital solutions se apna business grow karein'],
    'digital_web' => ['en' => 'Web Development', 'ur' => 'Web Development'],
    'digital_web_desc' => ['en' => 'Professional websites for your business', 'ur' => 'Aapke business ke liye professional websites'],
    'digital_ecommerce' => ['en' => 'E-Commerce', 'ur' => 'E-Commerce'],
    'digital_ecommerce_desc' => ['en' => 'Online stores with payment integration', 'ur' => 'Payment integration ke saath online stores'],
    'digital_design' => ['en' => 'Graphic Design', 'ur' => 'Graphic Design'],
    'digital_design_desc' => ['en' => 'Logos, banners, brochures & more', 'ur' => 'Logos, banners, brochures aur mazeed'],
    'digital_seo' => ['en' => 'SEO Services', 'ur' => 'SEO Services'],
    'digital_seo_desc' => ['en' => 'Rank higher on search engines', 'ur' => 'Search engines pe higher rank karein'],
    'digital_social' => ['en' => 'Social Media Marketing', 'ur' => 'Social Media Marketing'],
    'digital_mobile' => ['en' => 'Mobile Apps', 'ur' => 'Mobile Apps'],
    'digital_page' => ['en' => 'Page Management', 'ur' => 'Page Management'],
    'digital_content' => ['en' => 'Content Writing', 'ur' => 'Content Writing'],
    'digital_video' => ['en' => 'Video Editing', 'ur' => 'Video Editing'],
    'digital_view_all' => ['en' => 'View All Digital Services', 'ur' => 'Tamam Digital Services Dekhein'],
    'digital_cta_title' => ['en' => 'Ready to Go Digital?', 'ur' => 'Digital banna chahte hain?'],
    'digital_cta' => ['en' => 'Contact us today and let\'s transform your business with our digital services.', 'ur' => 'Aaj hi contact karein aur humare digital services se apna business transform karein.'],
    'digital_start' => ['en' => 'Start Your Project', 'ur' => 'Apna Project Shuru Karein'],

    // Business Directory
    'biz_title' => ['en' => 'Business Directory', 'ur' => 'Business Directory'],
    'biz_subtitle' => ['en' => 'Discover local businesses across Kasur District', 'ur' => 'Kasur District bhar ki local businesses discover karein'],
    'biz_add' => ['en' => 'Add Your Business', 'ur' => 'Apni Business Add Karein'],
    'biz_featured' => ['en' => 'Featured Only', 'ur' => 'Sirf Featured'],
    'biz_browse' => ['en' => 'Browse Businesses', 'ur' => 'Businesses Dekhein'],
    'biz_cta_title' => ['en' => 'Own a Business?', 'ur' => 'Aapki khud ki Business hai?'],
    'biz_cta' => ['en' => 'List your business in our directory and reach thousands of customers across Kasur District.', 'ur' => 'Apni business hamare directory mein list karein aur Kasur District bhar hazaron customers tak pahunchein.'],
    'biz_register' => ['en' => 'Register Now - Free!', 'ur' => 'Abhi Register Karein - Muft!'],

    // Jobs
    'jobs_title' => ['en' => 'Jobs Portal', 'ur' => 'Jobs Portal'],
    'jobs_subtitle' => ['en' => 'Find your dream job in Kasur District', 'ur' => 'Kasur District mein apni pasand ki job dhundhein'],
    'jobs_find' => ['en' => 'Find Jobs', 'ur' => 'Jobs Dhundhein'],
    'jobs_post' => ['en' => 'Post a Job', 'ur' => 'Job Post Karein'],
    'jobs_apply' => ['en' => 'Apply Now', 'ur' => 'Abhi Apply Karein'],
    'jobs_cta_title' => ['en' => 'Hiring? Post Your Job!', 'ur' => 'Hiring kar rahe hain? Job Post Karein!'],
    'jobs_cta' => ['en' => 'Reach thousands of job seekers in Kasur District. Post your job opening today.', 'ur' => 'Kasur District mein hazaron job seekers tak pahunchein. Aaj hi apni job opening post karein.'],
    'jobs_post_free' => ['en' => 'Post Job - Free!', 'ur' => 'Job Post Karein - Muft!'],

    // News
    'news_title' => ['en' => 'News Portal', 'ur' => 'News Portal'],
    'news_subtitle' => ['en' => 'Stay updated with the latest news from Kasur District', 'ur' => 'Kasur District ki latest news se updated rahein'],
    'news_breaking' => ['en' => 'BREAKING', 'ur' => 'BREAKING'],
    'news_read_more' => ['en' => 'Read More', 'ur' => 'Mazeed Padhhein'],
    'news_cta_title' => ['en' => 'Have News to Share?', 'ur' => 'Koi News share karna hai?'],
    'news_submit' => ['en' => 'Submit News', 'ur' => 'News Bhejein'],

    // Blog
    'blog_title' => ['en' => 'Blog', 'ur' => 'Blog'],
    'blog_read_more' => ['en' => 'Read More', 'ur' => 'Mazeed Padhhein'],
    'blog_subscribe' => ['en' => 'Subscribe', 'ur' => 'Subscribe Karein'],
    'blog_newsletter' => ['en' => 'Get the latest blog posts delivered to your inbox.', 'ur' => 'Latest blog posts apne inbox mein paayein.'],

    // Contact
    'contact_title' => ['en' => 'Contact Us', 'ur' => 'Hum se Contact Karein'],
    'contact_subtitle' => ['en' => 'Get in touch with us for event management and digital services', 'ur' => 'Event management aur digital services ke liye hum se rabta karein'],
    'contact_send' => ['en' => 'Send Message', 'ur' => 'Message Bhejein'],
    'contact_address' => ['en' => 'Address', 'ur' => 'Pata'],
    'contact_phone' => ['en' => 'Phone', 'ur' => 'Phone'],
    'contact_email_label' => ['en' => 'Email', 'ur' => 'Email'],
    'contact_hours' => ['en' => 'Working Hours', 'ur' => 'Working Hours'],
    'contact_hours_value' => ['en' => 'Mon - Sat: 9:00 AM - 6:00 PM', 'ur' => 'Som - Shan: 9:00 AM - 6:00 PM'],
    'contact_success' => ['en' => 'Thank you! Your message has been sent successfully. We will get back to you soon.', 'ur' => 'Shukriya! Aapka message kamyabi se bhej diya gaya hai. Hum jald hi aapko jawab denge.'],
    'contact_error' => ['en' => 'Something went wrong. Please try again later.', 'ur' => 'Kuch ghalat ho gaya. Dobara koshish karein.'],

    // Auth
    'login_title' => ['en' => 'Login', 'ur' => 'Login'],
    'login_welcome' => ['en' => 'Welcome back! Please login to your account.', 'ur' => 'Khush aamdeed! Apne account mein login karein.'],
    'login_email' => ['en' => 'Email Address', 'ur' => 'Email Address'],
    'login_password' => ['en' => 'Password', 'ur' => 'Password'],
    'login_btn' => ['en' => 'Login', 'ur' => 'Login'],
    'login_forgot' => ['en' => 'Forgot Password?', 'ur' => 'Password bhool gaye?'],
    'login_no_account' => ['en' => "Don't have an account?", 'ur' => 'Account nahi hai?'],
    'login_register' => ['en' => 'Register Here', 'ur' => 'Yahan Register Karein'],

    'register_title' => ['en' => 'Register', 'ur' => 'Register'],
    'register_subtitle' => ['en' => 'Create your account and get started today!', 'ur' => 'Apna account banayein aur aaj hi shuru karein!'],
    'register_name' => ['en' => 'Full Name', 'ur' => 'Pura Naam'],
    'register_phone' => ['en' => 'Phone Number', 'ur' => 'Phone Number'],
    'register_confirm_password' => ['en' => 'Confirm Password', 'ur' => 'Password Confirm Karein'],
    'register_city' => ['en' => 'City', 'ur' => 'Shehr'],
    'register_account_type' => ['en' => 'Account Type', 'ur' => 'Account Ki Type'],
    'register_personal' => ['en' => 'Personal Account', 'ur' => 'Personal Account'],
    'register_business' => ['en' => 'Business Account', 'ur' => 'Business Account'],
    'register_terms' => ['en' => 'I agree to the Terms of Service and Privacy Policy', 'ur' => 'Main Terms of Service aur Privacy Policy se mutafiq hoon'],
    'register_btn' => ['en' => 'Register Now', 'ur' => 'Abhi Register Karein'],
    'register_have_account' => ['en' => 'Already have an account?', 'ur' => 'Pehle se account hai?'],

    // Common
    'learn_more' => ['en' => 'Learn More', 'ur' => 'Mazeed Janein'],
    'view_all' => ['en' => 'View All', 'ur' => 'Tamam Dekhein'],
    'search' => ['en' => 'Search', 'ur' => 'Search'],
    'filter' => ['en' => 'Filter', 'ur' => 'Filter'],
    'all_cities' => ['en' => 'All Cities', 'ur' => 'Tamam Shehr'],
    'all_types' => ['en' => 'All Types', 'ur' => 'Tamam Types'],
    'all_categories' => ['en' => 'All Categories', 'ur' => 'Tamam Categories'],
    'no_results' => ['en' => 'No results found.', 'ur' => 'Koi nateeja nahi mila.'],
    'showing' => ['en' => 'Showing', 'ur' => 'Dikha rahe hain'],
    'previous' => ['en' => 'Previous', 'ur' => 'Pehla'],
    'next' => ['en' => 'Next', 'ur' => 'Agle'],
    'featured' => ['en' => 'Featured', 'ur' => 'Featured'],
    'read_more' => ['en' => 'Read More', 'ur' => 'Mazeed Padhhein'],
    'view_details' => ['en' => 'View Details', 'ur' => 'Details Dekhein'],
    'request_quote' => ['en' => 'Request Quote', 'ur' => 'Quote Mangein'],
    'views' => ['en' => 'views', 'ur' => 'views'],
    'reviews' => ['en' => 'reviews', 'ur' => 'reviews'],
    'deadline' => ['en' => 'Deadline', 'ur' => 'Aakhri tareekh'],
    'salary' => ['en' => 'Salary', 'ur' => 'Tankhwa'],
    'location' => ['en' => 'Location', 'ur' => 'Location'],
    'type' => ['en' => 'Type', 'ur' => 'Type'],
    'category' => ['en' => 'Category', 'ur' => 'Category'],
    'full_time' => ['en' => 'Full Time', 'ur' => 'Full Time'],
    'part_time' => ['en' => 'Part Time', 'ur' => 'Part Time'],
    'contract' => ['en' => 'Contract', 'ur' => 'Contract'],
    'freelance' => ['en' => 'Freelance', 'ur' => 'Freelance'],
    'privacy_policy' => ['en' => 'Privacy Policy', 'ur' => 'Privacy Policy'],
    'terms' => ['en' => 'Terms of Service', 'ur' => 'Terms of Service'],
    'follow_us' => ['en' => 'Follow Us', 'ur' => 'Humain Follow Karein'],
    'quick_links' => ['en' => 'Quick Links', 'ur' => 'Quick Links'],
    'our_services' => ['en' => 'Our Services', 'ur' => 'Hamari Khidmaat'],
    'contact_us' => ['en' => 'Contact Us', 'ur' => 'Contact'],
    'all_rights' => ['en' => 'All Rights Reserved.', 'ur' => 'Tamam huquooq mehfooz hain.'],
    'back_to_top' => ['en' => 'Back to Top', 'ur' => 'Wapas upar'],

    // CTA Section
    'cta_ready' => ['en' => 'Ready to Get Started?', 'ur' => 'Shuru karne ke liye tayyar hain?'],
    'cta_contact' => ['en' => 'Contact us today for your event management or digital service needs', 'ur' => 'Event management ya digital services ke liye aaj hi hum se contact karein'],
    'cta_contact_btn' => ['en' => 'Contact Us Now', 'ur' => 'Abhi Contact Karein'],

    // Stats
    'stat_events' => ['en' => 'Events Managed', 'ur' => 'Events Manage kiye'],
    'stat_businesses' => ['en' => 'Businesses Listed', 'ur' => 'Businesses List hain'],
    'stat_projects' => ['en' => 'Digital Projects', 'ur' => 'Digital Projects'],
    'stat_rating' => ['en' => 'Customer Rating', 'ur' => 'Customer Rating'],

    // Why Choose Us
    'why_title' => ['en' => 'Why Choose DigitalKasur?', 'ur' => 'DigitalKasur kyun chunein?'],
    'why_local' => ['en' => 'Local Expertise', 'ur' => 'Local Expertise'],
    'why_local_desc' => ['en' => 'We understand the local market and tailor our services to meet your specific needs in Kasur District.', 'ur' => 'Hum local market samajhte hain aur Kasur District mein aapki khaas zarooraton ke mutabiq khidmaat faraham karte hain.'],
    'why_fast' => ['en' => 'Fast Delivery', 'ur' => 'Jaldi Delivery'],
    'why_fast_desc' => ['en' => 'Quick turnaround times without compromising on quality. Get your project done on time, every time.', 'ur' => 'Quality se samjhauta kiye bina jaldi delivery. Har baar waqt par apna project mukammal karein.'],
    'why_affordable' => ['en' => 'Affordable Prices', 'ur' => 'Sasti Qeematen'],
    'why_affordable_desc' => ['en' => 'Competitive pricing that fits your budget. Get premium quality services at reasonable rates.', 'ur' => 'Aapke budget ke mutabiq pratiyoggi qeematen. Munasib rates par premium quality services paayein.'],

    // Process
    'process_title' => ['en' => 'Our Process', 'ur' => 'Hamara Tareeqa'],
    'process_subtitle' => ['en' => 'Simple and streamlined workflow', 'ur' => 'Aasaan aur streamlined workflow'],
    'process_consultation' => ['en' => 'Consultation', 'ur' => 'Mashwara'],
    'process_consultation_desc' => ['en' => 'We discuss your requirements and goals', 'ur' => 'Hum aapki zarooraton aur hadafon par guftagu karte hain'],
    'process_planning' => ['en' => 'Planning', 'ur' => 'Planning'],
    'process_planning_desc' => ['en' => 'We create a detailed plan and strategy', 'ur' => 'Hum detailed plan aur strategy banate hain'],
    'process_execution' => ['en' => 'Execution', 'ur' => 'Iraadah'],
    'process_execution_desc' => ['en' => 'We implement with attention to detail', 'ur' => 'Hum tafseel ki tawajjo se implement karte hain'],
    'process_delivery' => ['en' => 'Delivery', 'ur' => 'Delivery'],
    'process_delivery_desc' => ['en' => 'We deliver and provide ongoing support', 'ur' => 'Hum deliver karte hain aur masroofi sahara faraham karte hain'],

    // WhatsApp
    'whatsapp_chat' => ['en' => 'Chat with us on WhatsApp', 'ur' => 'WhatsApp par baat karein'],
    'whatsapp_message' => ['en' => 'Hi! I am visiting digitalkasur.com and need help with...', 'ur' => 'Assalam o Alaikum! Main digitalkasur.com se aa raha hoon aur mujhe help chahiye...'],
];

/**
 * Translate function
 */
function __($key, $lang = null) {
    global $translations, $current_lang;

    $lang = $lang ?: $current_lang;

    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }

    // Fallback to English
    if (isset($translations[$key]['en'])) {
        return $translations[$key]['en'];
    }

    // Return the key itself if no translation found
    return $key;
}

/**
 * Echo translation
 */
function _e($key, $lang = null) {
    echo __($key, $lang);
}

/**
 * Get current language
 */
function get_lang() {
    global $current_lang;
    return $current_lang;
}

/**
 * Get language toggle URL
 */
function lang_toggle_url() {
    $current_url = $_SERVER['REQUEST_URI'];
    $target_lang = get_lang() === 'en' ? 'ur' : 'en';

    // Remove existing lang parameter
    $url = preg_replace('/[?&]lang=[^&]+/', '', $current_url);

    // Add new lang parameter
    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separator . 'lang=' . $target_lang;
}

/**
 * Get language toggle label
 */
function lang_toggle_label() {
    return get_lang() === 'en' ? 'اردو' : 'English';
}
?>
