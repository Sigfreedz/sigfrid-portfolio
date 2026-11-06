import java.util.Scanner;
public class BarreraInputBasedNumberedTriangle{
	public static void main(String[] args){
		Scanner userInput = new Scanner(System.in);
		System.out.print("Input a number as the height of the triangle: ");
		int triangleHeight = userInput.nextInt();
		
		//     1
		//    1 2
		//   1 2 3
		//  1 2 3 4
		int spaceCounter = triangleHeight;
		if(triangleHeight>0){
			for(int heightCounter = 1; heightCounter<=triangleHeight; heightCounter++){
				for(int spaces = spaceCounter;spaces>0;spaces--){
					System.out.print(" ");
				}
				spaceCounter--;
				for(int numberCounter = 1; numberCounter<=heightCounter;numberCounter++){
					System.out.print(numberCounter+" ");
				}
				System.out.println();
			}
			spaceCounter+=2;
			for(int heightCounter = triangleHeight-1; heightCounter>0; heightCounter--){
				for(int spaces = 0;spaces<spaceCounter;spaces++){
					System.out.print(" ");
				}
				spaceCounter++;
				for(int numberCounter = heightCounter; numberCounter>0;numberCounter--){
					System.out.print(numberCounter+" ");
				}
				System.out.println();	
			}
		}else{
			System.out.println("Invalid Input");
		}
		
			
		
	}
}
