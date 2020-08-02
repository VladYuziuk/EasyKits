<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use vyuziuk\EasyKits\Forms\CreateKitForm;
use vyuziuk\EasyKits\i18n\LanguageFactory;
use vyuziuk\EasyKits\KitManager;
use vyuziuk\EasyKits\Main;

/**
 * Class EasyKit
 * @package vyuziuk\EasyKits\Commands
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class EasyKit extends Command{
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->setPermission(Main::getInstance()->getConfig()->get("adminPermission"));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("command.non.player"));
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage(LanguageFactory::getLanguage()->translateString("command.usage"));
            return;
        }
        switch($args[0]){
            case "new":
            case "create":
                $sender->sendForm(new CreateKitForm());
                break;
            case "remove":
            case "delete":
                KitManager::getInstance()->removeKit($args[0]);
                break;
        }
    }
}