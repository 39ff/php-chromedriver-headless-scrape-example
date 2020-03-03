<?php
require '../vendor/autoload.php';
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

$host = 'http://127.0.0.1:4444';
$proxy = [
    'proxyType' => 'manual',
    'httpProxy' => 'your-squid-proxy:3128',
    'sslProxy' => 'your-squid-proxy:3128',
];
$driver = null;

try {
    $options = new ChromeOptions();
    $options->addArguments([
         '--headless',
        // '--window-size=700,700',
        '--ignore-certificate-errors',
        '--disable-popup-blocking',
        '--disable-web-security',
        '--start-maximized',
        '--incognito',
        '--no-sandbox',
        '--disable-infobars',
        '--disable-dev-shm-usage',
        '--disable-browser-side-navigation',
        '--disable-gpu',
        '--disable-features=VizDisplayCompositor',
    ]);


    $options->setExperimentalOption('excludeSwitches', ['enable-automation']);
    $caps = DesiredCapabilities::chrome();
    $caps->setCapability('proxy',$proxy);
    $caps->setCapability('pageLoadStrategy', 'none');
    $caps->setCapability(ChromeOptions::CAPABILITY, $options);
    $driver = RemoteWebDriver::create($host, $caps);

    $driver->get('https://ipv6-test.com/');
    $driver->wait(60, 500)->until(
        WebDriverExpectedCondition::elementTextMatches(WebDriverBy::cssSelector("#score .row .text-right h2"),"/[0-9]+ \/ [0-9]+/")
    );
    //ページのHTMLソース
    $source = $driver->getPageSource();
    var_dump($source);
    $driver->wait(5);
    $driver->takeScreenshot('myip2.png');

}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    //var_dump($e);
    var_dump($e->getMessage());
}

try{
    //確実に終了する
    if($driver != null && $driver instanceof RemoteWebDriver){
        $driver->close();
    }
}catch (\Facebook\WebDriver\Exception\WebDriverException $e){
    var_dump($e->getMessage());
}