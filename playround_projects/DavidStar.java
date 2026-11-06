import java.util.*;
public class DavidStar{
	public static void main(String[] args){
		Scanner userInput = new Scanner(System.in);
		int starSize;
		while(true){
			System.out.print("Enter the size of your David's Star: ");
			starSize=userInput.nextInt();
			if(starSize%2==0||starSize<7||starSize>15){
				System.out.println("INVALID SIZE! MUST BE ODD NUMBER,(7-15)");
			}else{
				break;
			}
		}
		DrawStarOfDavid(starSize);
	}
	public static void DrawStarOfDavid(int size){
		int starCount = 1;
		int lineCount = (int)Math.floor(size/2);
		
		//UPPER PART
		while(lineCount>=3){
			for(int counter=0;counter<lineCount;counter++){
				System.out.print("_");
			}
			for(int counter=0;counter<starCount;counter++){
				System.out.print("*");
			}
			for(int counter=0;counter<lineCount;counter++){
				System.out.print("_");
			}
			starCount+=2;
			lineCount--;
			System.out.println();
		}
		
		//MIDDLE PART
		for(int counter=0;counter<size;counter++){
			System.out.print("*");
		}
		System.out.println();
		for(int counter=0;counter<size;counter++){
			if(counter==0||counter==(size-1)){
				System.out.print("_");
			}else{
				System.out.print("*");
			}	
		}
		System.out.println();
		for(int counter=0;counter<size;counter++){
			System.out.print("*");
		}
		System.out.println();
		
		//LOWER PART
		starCount-=2;
		lineCount++;
		while(lineCount<=(int)Math.floor(size/2)){
			for(int counter=0;counter<lineCount;counter++){
				System.out.print("_");
			}
			for(int counter=0;counter<starCount;counter++){
				System.out.print("*");
			}
			for(int counter=0;counter<lineCount;counter++){
				System.out.print("_");
			}
			starCount-=2;
			lineCount++;
			System.out.println();
		}
	}	
}