 #include <stdio.h>          
 #include <stdlib.h>         
 #include <sys/types.h>
 #include <sys/socket.h>     
 #include <errno.h>          
 #include <netinet/in.h>     
 #include <arpa/inet.h>      
 #include <unistd.h>
 #include <sys/utsname.h>
 #include <string.h>
 #include <iostream>
 #include <string>
 #include <iomanip>
 #include <fstream>
 
 #define MAXLEN 1024
 using namespace std;
  
 int main()
 {
     u_char no = 0;
     u_int yes = 1;      

     int send_s, recv_s; 
     u_char ttl;
     struct sockaddr_in mcast_group;
     struct ip_mreq mreq;
     struct utsname name;
     int i, j, n, x;
     int count;
     struct in_addr binary_ip;
     unsigned int len;
     struct sockaddr_in from;
     char message [MAXLEN+1];
     char ipaddr[80];
    
     char lastoctet[4];
     char ips[] = {'1','2','3','4','5','6'};
     int status[6];
     fstream my_file;
/*********************************************************************** 
*  Open the GXR2 status file and pull down a copy of the status table  * 
*  The status for each zone is represented by a number from 0 to 6.    * 
*  0 - no input device selected                                        *
*  1 - sirst input device selected                                     *
*  2 - second input device selected                                    *
*  3 - third input device selected                                     *
*  4 - fourth input device selected                                    *
*  5 - fifth input device selected                                     *
*  6 - sixth input device selected                                     *
************************************************************************
  
	my_file.open("/home/pi/GXR2status.txt", ios::in);
	if (!my_file) {
		cout << "File not found!";
	}
	else {
		i=0;
		while(my_file >> status[i]) { 
			i++;
		}
		my_file.close();
	}

//  Open a udp socket on port 6001         //
     
     memset(&mcast_group, 0, sizeof(mcast_group));
     mcast_group.sin_family = AF_INET;
     mcast_group.sin_port = htons((unsigned short int)strtol("6001", NULL, 0));
     mcast_group.sin_addr.s_addr = INADDR_ANY;
     
     recv_s=socket(AF_INET, SOCK_DGRAM, 0);
     setsockopt(recv_s, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(yes));     
     bind(recv_s, (struct sockaddr*)&mcast_group, sizeof(mcast_group));

    	// Prepare it to receive multicast traffic // 

     mreq.imr_multiaddr = mcast_group.sin_addr;
     mreq.imr_interface.s_addr = htonl(INADDR_ANY);
     setsockopt(recv_s, IPPROTO_IP, IP_ADD_MEMBERSHIP, &mreq, sizeof(mreq));
     
     //  Add six IP Addresses to the multicast group //
     
     for(i=0; i<6; i++)
     {
          lastoctet[0] = ips[i];
          lastoctet[1] = '\0';
          sprintf(ipaddr, "232.0.0.%s", lastoctet);
          mcast_group.sin_addr.s_addr = inet_addr(ipaddr);
          mreq.imr_multiaddr = mcast_group.sin_addr;
          setsockopt(recv_s, IPPROTO_IP, IP_ADD_MEMBERSHIP, &mreq, sizeof(mreq));
     }
     
     //  Check for multicast status change messages from the Niles GXR2  //
 
	for (;;) 
	{
	       read(recv_s, message, 1024);
	       i=message[2]-33;  // zone //
	       j=message[11];    // status //

          //  Update the status file if there are changes. //

        if (j!=status[i])
        {
			status[i] = j;
			my_file.open("/home/pi/GXR2status.txt", ios::out);
			if (!my_file) {
				cout << "File not found!";
			}
			else {
				for(i=0; i<6; i++) 
				{
					my_file << status[i]; 
				}
			}
			my_file.close();
		}
	}              
	return 0;
 }

