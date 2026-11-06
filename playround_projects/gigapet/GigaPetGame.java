package gigapet;
public class GigaPetGame{

	public static void main(String[] args){
		GigaPet newGigaPet = null;
		Scanner userInput = new Scanner(System.in);
		boolean isRunning = true;
		
		
		while(isRunning){
			if(newGigaPet == null){
				int choice = 0;
				while(choice<=0||choice>2){
					System.out.println("==== MAIN MENU ====");
					System.out.println("1.Create new pet");
					System.out.println("2. Exit");
					System.out.print("Enter your choice: ");
					choice = userInput.nextInt();
					switch(choice){
						case 1:
							System.out.print("Enter your pet's name: ");
							userInput.nextLine();
							String gigaPetName = userInput.nextLine();
							System.out.print("Enter your pet's type (Dog, Cat, Eagle, Dragon): ");
							String gigaPetType = userInput.nextLine();
							newGigaPet = new GigaPet(gigaPetName, gigaPetType);
							break;
						case 2:
							isRunning = false;
							break;
						default:
							System.out.println("Invalid Input");
					}
					
				}
			}else{
				int choice = 0;
				while(choice<=0||choice>4){
					System.out.println("\n=== MAIN MENU ===");
					System.out.println("1. Feed "+newGigaPet.gigaPetName+" the "+newGigaPet.gigaPetType);
					System.out.println("2. Play with "+newGigaPet.gigaPetName+" the "+newGigaPet.gigaPetType);
					System.out.println("3. Check "+newGigaPet.gigaPetName+" the "+newGigaPet.gigaPetType+"'s Status");
					System.out.println("4. Exit");
					System.out.print("Enter your choice: ");
					
					choice = userInput.nextInt();
					
					switch(choice){
						case 1: 
							int amountToFeed=0;
							while(amountToFeed<=0){
								if(amountToFeed<=0){
									System.out.print("\nHow many times do u you want to feed "+newGigaPet.gigaPetName+"? ");
									amountToFeed = userInput.nextInt();
									newGigaPet.feed(amountToFeed);
								}else{
									System.out.println("Invalid Input");
								}		
							}
							break;
						case 2:
							int amountToPlay=0;
							while(amountToPlay<=0){
								if(amountToPlay<=0){
									System.out.print("\nHow many times do u you want to play with "+newGigaPet.gigaPetName+"? ");
									amountToPlay = userInput.nextInt();
									newGigaPet.play(amountToPlay);
								}else{
									System.out.println("Invalid Input");
								}		
							}
							break;
						case 3:
							newGigaPet.printStatus();
							break;
						case 4:
							isRunning = false;
						
					}
				}
			}
		}
	}
}