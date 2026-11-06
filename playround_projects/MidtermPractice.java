import java.util.*;
class Calculations{
	public static int add(int firstNumber, int secondNumber){
		return firstNumber+secondNumber;
	}
}

class Bank{
	
	int balance;
	String bankOperator;
	
	public Bank(int balance, String bankOperator){
		this.balance = balance;
		this.bankOperator = bankOperator;
	}
}

class ATM{
	static List<Bank> bankList = new ArrayList<>();
	
	public ATM(){
		int[] initialBalance = {100,200,300,400,500,600,700,800,900};
		String[] bankOperators = {"Metrobank","BDO","BPI","PNB","Chinabank"};
		
		for(int operatorIndexes=0;operatorIndexes<bankOperators.length;operatorIndexes++){
			for(int balanceIndexes=0;balanceIndexes<initialBalance.length;balanceIndexes++){
				Bank newBank = new Bank(initialBalance[balanceIndexes],bankOperators[operatorIndexes]);
				bankList.add(newBank);
			}
		}
	}
	
	public void balanceInquiry(int accountNumber){
		System.out.println("Bank: "+bankList.get(accountNumber).bankOperator+"\n"+"Balance: "+bankList.get(accountNumber).balance);
	}
	public void withraw(int accountNumber,int amount){
		bankList.get(accountNumber).balance -= amount;
	}
	public void deposit(int accountNumber,int amount){
		bankList.get(accountNumber).balance += amount;
	}
}

public class MidtermPractice{
	public static void main(String[] args){
		ATM euroNet = new ATM();
		
		euroNet.balanceInquiry(20);
		euroNet.withraw(20,100);
		euroNet.balanceInquiry(20);
		// for(int bankIndex = 0; bankIndex<euroNet.bankList.size();bankIndex++){
			// euroNet.balanceInquiry(bankIndex);
			// System.out.println();
		// }	
	}
}