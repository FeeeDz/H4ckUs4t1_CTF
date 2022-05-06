#include <stdio.h>
#include <stdlib.h>

void init(){
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    setbuf(stderr, NULL);
}


int main(){

char buf[100];

init();
puts("Welcome back boyz!\n");
puts("Now it's time to jump to the flag!\n");
puts("Enter your name: ");
gets(buf);
}


int win(){
    system("cat flag.txt");
}