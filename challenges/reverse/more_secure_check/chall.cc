#include <stdio.h>
#include <stdarg.h>
#include <cstring>

char flag[512];
char password[] = "Th1s_1S_4_M0r3_S3cUr3_P4ssw0rd";


bool is_printable( char c )
{
    return !( c & (1 << 7) );
}

void c(char *buf, ...)
{
	va_list args;
	va_start (args, buf);    

	char arg = va_arg(args, int);
	
	while( arg ) {
		sprintf(buf, "%s%c", buf, arg);
		arg = va_arg(args, int);
	}

	va_end (args);                  
}

void print_flag(){
		c(flag,'I', 'T', 'T', '{', 'S', 't', '0', 'P', '_', 't', '0', '_', 'S', 't', '3', '4', 'L', '_', '0', 'u', 'R', '_', 'F', 'l', '4', 'g', '_', '}');

	puts(flag);
}

int main(){
 	
	char input[32];
	printf("Welcome to the new safe check!\n");
	printf("Only authorized users can enter.\n");
	printf("Enter your password: ");
	scanf("%s", input);
	if(strcmp(input, password) == 0){
		printf("Access granted!\n");
		printf("Today you can't steal my flag :P\n");
		return 0;
	}else{
		printf("Access denied!\n");
	}
}