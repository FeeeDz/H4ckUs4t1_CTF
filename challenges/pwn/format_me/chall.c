#include <stdio.h>
#include <stdlib.h>

void init(){
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    setbuf(stderr, NULL);
}

void vuln(){
char flag[18]="ITT{U_f0UnD_M3!!}";
    int *ptr=flag;
    char input[100];
    puts("Welcome back to another pwn boyz!\n");
    puts("Now it's time to found the flag in the memory!\n");
    puts("Input your name: ");
    scanf("%s",input);

    printf("Hello: ");
    printf(input);
}

int main(){
    init();
    vuln();
}


// %23$s


//remote %7$s