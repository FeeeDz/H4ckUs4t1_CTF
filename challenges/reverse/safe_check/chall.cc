#include <stdio.h>
#include <stdarg.h>
#include <cstring>

char flag[512];
char password[] = "S3cUr3_P4ssw0rd";


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
	for(int i = 0; i < strlen(flag)-3; i++){
        printf("%c", flag[i]);
    }
}

int main(){
 	
	c(flag,'I', 'T', 'T', '{', 'N', '3', 'v', '3', 'r', '_', 'P', 'u', 't', 'z', '_', 'P', 'l', '4', '1', 'n', '_', 'T', '3', 'x', 'T', '}', '\n');
	char input[32];
	printf("Welcome to the secret safe!\n");
	printf("Only authorized users can enter.\n");
	printf("Enter your password: ");
	scanf("%s", input);
	if(strcmp(input, password) == 0){
		printf("Access granted!\n");
		printf("Here is your flag: ");
		print_flag();
		return 0;
	}else{
		printf("Access denied!\n");
	}
}