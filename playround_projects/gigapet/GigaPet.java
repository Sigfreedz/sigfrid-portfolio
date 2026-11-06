package gigapet;
public class GigaPet{
	String gigaPetName;
	String gigaPetType;
	int hunger = 50;
	int happiness = 50;
	
	public GigaPet(String gigaPetName,String gigaPetType){
		this.gigaPetName = gigaPetName;
		this.gigaPetType = gigaPetType;
		this.hunger = hunger;
		this.happiness = happiness;
	}
	
	public void feed(int amountToFeed){
		for(int feedCounter = 0; feedCounter<amountToFeed; feedCounter++){
			if(hunger>=0){
				System.out.println(gigaPetName+": Yum!");
				hunger-=10;
				happiness+=5;
			}
		}
		System.out.println("You fed "+gigaPetName+" "+amountToFeed+"times");

	}
	
	public void play(int amountToPlay){
		for(int playCounter = 0; playCounter<amountToPlay; playCounter++){
			System.out.println(gigaPetName+": Woooohoooo!");
			hunger+=5;
			happiness+=10;
			
		}
		System.out.println("You played with "+gigaPetName+" "+amountToPlay+"times");
	}
	
	public void printStatus(){
		happiness = (happiness > 100) ? 100 : happiness;
		hunger = (hunger < 0) ? 0 : hunger;
		hunger = (hunger > 100) ? 100 : hunger;
		
		System.out.println("\n-------------------------------------------");
		System.out.println(gigaPetName+"the"+gigaPetType+"'s Status: \n");
		System.out.println("Hunger: "+this.hunger+"/100");
		System.out.println("Happiness: "+this.happiness+"/100");
		System.out.println("-------------------------------------------\n");
		
	}
	
}