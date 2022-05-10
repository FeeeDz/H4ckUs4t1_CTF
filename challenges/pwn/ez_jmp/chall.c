#include <stdio.h>
#include <stdlib.h>
#include <string.h>

void init(){
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    setbuf(stderr, NULL);
}
void vuln(){
    char input[100];
    char buf[50];
    puts("Input your name:");
    scanf("%s",input);
    strcpy(buf,input);
}


int main(){

init();
puts("Welcome back boyz!\n");
puts("Now it's time to jump to the flag!\n");
vuln();

return 0;
}


int win(){
    system("cat flag.txt");
}