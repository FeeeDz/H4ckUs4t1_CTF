#include <stdio.h>
#include <stdlib.h>
#include <string.h>


void init(){
    setbuf(stdout, NULL);
    setbuf(stdin, NULL);
    setbuf(stderr, NULL);
}

void hello(char *string){
    char input[128];
    strcpy(input, string);
    printf("Hello: ");
    printf(input);
    printf("!\n");
    return;
}
int main(){
    int size = 0;
    char *string=(char*) malloc(0);
    printf("Welcome back to the last one pwn of this edition boyz!\n");
    printf("Try to pwn me :D\n");
    printf("What's your name?\n");
    while(1){
        char tmp;
        scanf("%c", &tmp);
        ++size;
        string=(char*) realloc(string, size* sizeof(char));
        if(tmp == "\n"){
            string[size-1] = '\0';
            break;
        }else{
            string[size-1] = tmp;
        }
    }
    hello(string);
    free(string);
    return 0;
}