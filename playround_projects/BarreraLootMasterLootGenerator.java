//Your program must generate  a random number (quantity) and type to determine the rarity of the loot
//EXAMPLE OUTPUT DISPLAY
//======= You have defeated the BOSS =========
//---------
//======== You have leveled up (+2) ==========
//
//Open loot crate? yes/no: yes/no
//--- Opening loot crate---
//item 1 [Common] item2[Uncommon] etc...

import java.util.*;
public class BarreraLootMasterLootGenerator{
	public static void main(String[] args){
		Random randomNumber = new Random();
		Scanner userInput = new Scanner(System.in);
		boolean isRunning = true;
		int level = 100;
		String[] items = {"[Common] Rusty Sword","[Uncommon] Iron Dagger","[Rare] Orc Axe","[Epic] Phoenix Spear","[Legendary] Mjolnir","[Mythical] Gungnir"};
		System.out.println("=====LOOT MASTER=====");
		while(isRunning){
			int choice = 0;
			int bossHealth = randomNumber.nextInt(9999)+1000;
			System.out.printf("\nYou are currently level:%d\n\n------You have encountered a BOSS [%dHP]------\n",level ,bossHealth);
			System.out.println("1. Abyssal Arrow");
			System.out.println("2. Atomic Bomb");
			System.out.println("3. Infinite Void");
			System.out.println("[Other]. Exit");
			System.out.print("Which skill do you want to use?: ");
			choice = userInput.nextInt();
			
			if(choice>=1&&choice<4){
				System.out.println("You have dealt 999999 dmg!");
				System.out.println("======= You have defeated the BOSS ========");
				System.out.println("  \n---------RAHHHHHHHHHHHHH!--------------\n");
				System.out.println("======= You have leveled up (+2) ========");
				level+=2;
				int quantity = randomNumber.nextInt(10)+1;
				System.out.printf("======= You have found %d crate/s ========\n",quantity);
				System.out.print("\nOpen Crates?(input 1 if yes): ");
				int openCrateChoice = userInput.nextInt();
				if(openCrateChoice==1){
					
					for(int quantityCount = 0;quantityCount<quantity;quantityCount++){
						int randomRaritySeed = randomNumber.nextInt(99)+1;
						int rarity;
						if(randomRaritySeed<40){
							rarity = 0;
						}else if(randomRaritySeed<70){
							rarity = 1;
						}else if(randomRaritySeed<90){
							rarity = 3;
						}else if(randomRaritySeed<97){
							rarity = 4;
						}else{
							rarity = 5;
						}
						System.out.printf("Item %d: %s\n",quantityCount+1,items[rarity]);
					}
				}else{
					System.out.println("You have escaped! Thanks for playing LootMaster, Goodbye!");
					isRunning = false;
				}
				
			}else{
				System.out.println("You have escaped! Thanks for playing LootMaster, Goodbye!");
				isRunning = false;
			}
		}
	}
}