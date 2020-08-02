<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use vyuziuk\EasyKits\i18n\LanguageFactory;
use vyuziuk\EasyKits\KitManager;
use vyuziuk\EasyKits\Main;
use vyuziuk\EasyKits\Provider\AsyncLibDatabase;

/**
 * Class Kit
 * @package vyuziuk\EasyKits\Commands
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class Kit extends Command{
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("command.non.player"));
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("command.usage"));
            return;
        }
        if(!KitManager::getInstance()->kitExists($args[0])){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("kit.not.found", [$args[0]]));
            return;
        }
        $kit = KitManager::getInstance()->getKitByName($args[0]);
        if(!$sender->hasPermission($kit->getPermission())){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("kit.no.permission"));
            return;
        }
        AsyncLibDatabase::getInstance()->selectPlayer($sender, function(array $rows) use ($sender, $kit){
            if(!empty($rows)){
                $row = $rows[0];
                if($row["time"] > time()){
                    if(Main::getInstance()->getConfig()->get("unlimited")){
                        if($sender->hasPermission(Main::getInstance()->getConfig()->get("unlimitedPermission"))){
                            KitManager::getInstance()->giveKit($sender, $kit);
                            return;
                        }
                    }
                    $time = $row["time"] - time();
                    $sender->sendMessage(LanguageFactory::getLanguage()->translateString("kit.wait.message", [($time/3600%24), ($time/60%60)]));
                    return;
                }else{
                    KitManager::getInstance()->giveKit($sender, $kit);
                }
            }
        });
    }
}