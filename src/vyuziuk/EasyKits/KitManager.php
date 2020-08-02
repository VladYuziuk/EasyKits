<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits;

use pocketmine\Player;
use pocketmine\utils\Config;
use vyuziuk\EasyKits\Exceptions\CloneFoundException;
use vyuziuk\EasyKits\i18n\LanguageFactory;
use vyuziuk\EasyKits\Objects\Kit;
use vyuziuk\EasyKits\Provider\AsyncLibDatabase;

/**
 * Class KitManager
 * @package vyuziuk\EasyKits
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class KitManager{
    /** @var Kit[]  */
    private $kits = [];

    /** @var KitManager */
    private static $instance;

    public function __construct(){
        self::$instance = $this;
        $folder = Main::getInstance()->getDataFolder();

        $kitCount = 0;
        $kitList = (new Config($folder . 'kitList.json', Config::JSON))->getAll();
        foreach($kitList as $kitName => $kitObject){
            $this->kits[$kitName] = Kit::fromArray($kitObject);
            $kitCount++;
        }

        Main::getInstance()->getLogger()->notice(LanguageFactory::getLanguage()->translateString("kits.loaded.notice", [$kitCount]));
    }

    /**
     * @return KitManager
     */
    public static function getInstance(): KitManager{
        return self::$instance;
    }

    public function saveData(): void{
        $cfg = new Config(Main::getInstance()->getDataFolder() . 'kitList.json', Config::JSON);
        //parsing kits
        $kits = [];
        foreach($this->kits as $name => $kit){
            $kits[$name] = $kit->toArray();
        }
        $cfg->setAll($kits);
        $cfg->save();
        Main::getInstance()->getLogger()->notice(LanguageFactory::getLanguage()->translateString("kits.saving.notice"));
    }

    public function removeKit(string $name): void{
        if(isset($this->kits[$name])){
            unset($this->kits[$name]);
        }
    }

    public function getKitByName(string $name): Kit{
        return $this->kits[$name];
    }

    public function kitExists(string $name): bool{
        return isset($this->kits[$name]);
    }

    public function giveKit(Player $player, Kit $kit): void{
        AsyncLibDatabase::getInstance()->selectPlayer($player, function(array $rows) use ($kit, $player){
            if(empty($rows)){
                AsyncLibDatabase::getInstance()->newPlayer($player, $kit);
            }else{
                $row = $rows[0];
                if($row["time"] <= time()){
                    AsyncLibDatabase::getInstance()->newPlayer($player, $kit);
                }
            }
        });
        $player->getInventory()->setContents($kit->getInventory());
        $player->getArmorInventory()->setContents($kit->getArmorInventory());
    }

    public function addKit(Kit $kit): void{
        if(isset($this->kits[$kit->getName()])){
            throw new CloneFoundException(sprintf("Kit with the name %s was already created.", $kit->getName()));
        }
        $this->kits[$kit->getName()] = $kit;
    }
}