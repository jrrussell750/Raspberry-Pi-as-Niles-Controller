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
 
/*****************************************************************
 *  Compile me using the following command:  g++ getstatus.cpp   *
 * 								 *
 ****************************************************************/
 int main()
 {
     u_char no = 0;
     u_char ttl;
     u_int yes = 1;      
     struct sockaddr_in mcast_group;
     struct ip_mreq mreq;
     struct utsname name;
     struct in_addr binary_ip;
     struct sockaddr_in from;
     int i, j, k, s, x;
     int send_s, recv_s; 
     int count;
     int status[12];  
     char *p=NULL, *e, *ip_addr; 
     unsigned int len;
     char message [MAXLEN+1];
     char ipaddr[80];
     char string_in[4];
     char local_ip[80];
     char lastoctet[4];
     char ips[] = {'1','2','3','4','5','6'};
     char ipzero[] = "0.0.0.0";
     const char *command;
     FILE * fp;
     size_t n;
     
/*********************************************************************** 
*  Open the GXR2 status file and pull down a copy of the status table. * 
*  The status table consists of 12 integers.  The first six integers   *
*  represent the status for each of six zones.  Each one consists of a *
*  number from 0 to 6, as shown below:                                 *
*                                                                      *
*  0 - no input device selected                                        *
*  1 - sirst input device selected                                     *
*  2 - second input device selected                                    *
*  3 - third input device selected                                     *
*  4 - fourth input device selected                                    *
*  5 - fifth input device selected                                     *
*  6 - sixth input device selected                                     *
*                                                                      *
*  The second six integers represent the volume level of each zone.    *
*  Volume levels range from 0 - 100.                                   *
*                                                                      *
***********************************************************************/

 
 
/***************************************************************
*  Check for the existence of the GXR2status.txt file          *
*  If it doesn't exist, create it and populate it with zeroes  *
*                                                              *
***************************************************************/
 
     fstream my_file; 
     my_file.open("/home/pi/GXR2status.txt", ios::in);
     if (!my_file) {
	  cout << "Creating file GXR2status.txt" << endl;
	  my_file.open("/home/pi/GXR2status.txt", ios::out);
	  for(i=0; i<12; i++) {
	       my_file << "0"  << endl;
	  }
	  my_file.close();
	  my_file.open("/home/pi/GXR2status.txt", ios::in);
     }
	      
     i=0;
     while(my_file >> string_in) {
	  status[i] = stoi(string_in); 
	  i++;
     }
     my_file.close();
	     
		
//  The niles GXR2 will always be connected to eth0  //
//  Therefore the multicast socket must be bound to that interface  //

//  Get the IP address for eth0 unless it doesn't exist  //

        fp = popen("ifconfig", "r");
        if (fp) {
                getline(&p, &n, fp);
                while ((p = strstr(p, "wlan0: ")) == NULL) {
                        getline(&p, &n, fp);
	        }
                getline(&p, &n, fp);
                if ((p = strstr(p, "inet ")) != NULL) {
                        p+=5;
                        e = strchr(p, ' ');
                        *e = '\0';
			ip_addr = p;
	        }

/********************************************************************
 * If eth0 is not assigned an IP, let raspbian choose the interface *
 * for the bind by setting ip_addr to 0.0.0.0                       *
 * This will prevent a segmentation fault due to no IP address      *
 * The netstat -gn command can be used to view the multicast        *  
 * groups, if problems arise.                                       *
 *******************************************************************/
 
		else {
			ip_addr  = ipzero;
	        }
        }
        pclose(fp);

//  Open a udp socket on port 6001       //
     
     memset(&mcast_group, 0, sizeof(mcast_group));
     mcast_group.sin_family = AF_INET;
     mcast_group.sin_port = htons((unsigned short int)strtol("6001", NULL, 0));
     mreq.imr_interface.s_addr = inet_addr(ip_addr);         
     recv_s=socket(AF_INET, SOCK_DGRAM, 0);
     setsockopt(recv_s, SOL_SOCKET, SO_REUSEADDR, &yes, sizeof(yes));     
     bind(recv_s, (struct sockaddr*)&mcast_group, sizeof(mcast_group));
  
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
	       i=message[2]-33;  // zone   //
	       j=message[11];    // status //
	       k=message[20];    // volume //
	       
          //  Update the statuclose(my_file)s file if there are changes. //

        if (j!=status[i] || k!=status[i+6])
        {
			status[i] = j;
			status[i+6] = k;
			my_file.open("/home/pi/GXR2status.txt", ios::out);
			if (!my_file) {
				cout << "File not found!";
			}
			else {
				for(i=0; i<12; i++) 
				{
					my_file << to_string(status[i]) << '\n';
				}
			}
			my_file.close();
		}
	}              
	return 0;
 }
