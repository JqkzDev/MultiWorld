<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\world\WorldCreationOptions;
use function is_numeric;
use function mt_rand;

class CreateSubCommand implements SubCommand {

	public function execute(CommandSender $sender, array $args, string $name): void {
		if(!isset($args[0])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "create-usage"));
			return;
		}

		if(MultiWorld::getInstance()->getServer()->getWorldManager()->isWorldGenerated($args[0])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "create-exists", [$args[0]]));
			return;
		}

		$seed = mt_rand();
		if(isset($args[1]) && is_numeric($args[1])) {
			$seed = (int)$args[1];
		}

		$generator = WorldUtils::getGeneratorByName($generatorName = $args[2] ?? "normal");
		if($generator === null) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "create-gennotexists", [$generatorName]));
			return;
		}

		Server::getInstance()->getWorldManager()->generateWorld(
			name: $args[0],
			options: WorldCreationOptions::create()
				->setSeed($seed)
				->setGeneratorClass($generator->getGeneratorClass())
		);

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "create-done", [$args[0], (string)$seed, $generatorName]));
	}
}
