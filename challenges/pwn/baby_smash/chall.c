#include <stdio.h>
#include <stdlib.h>

void init(){
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    setbuf(stderr, NULL);
}

int main(){

    int flag=0;
    char buf[100];
    
    init();

    puts("Welcome to the baby smash challenge!");
    puts("Enter your name: ");
    gets(buf);

    if(flag != 0){
        system("cat flag.txt");
    }else{
        puts("You are not allowed to see the flag");
    }
}