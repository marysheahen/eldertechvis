function [f1 f2 f3 f4 r1 r2 r3 r4 datetime ] = importBedSensorData(fname, trans)
%IMPORTBEDSENSORDATA Imports sensor data and info from .bed file
%   <TODO> Write Explanatio%//print "inside the function"
fid = fopen(fname);
dinfo = dir(fname)

bytes = dinfo.bytes;

state = 0;
status = -1;

blk = 0;;

data = fread(fid);
n = 1;

f1_u = zeros(100*60*60,1);
f2_u = zeros(100*60*60,1);
f3_u = zeros(100*60*60,1);
f4_u = zeros(100*60*60,1);

r1_u = zeros(100*60*60,1);
r2_u = zeros(100*60*60,1);
r3_u = zeros(100*60*60,1);
r4_u = zeros(100*60*60,1);

while ((n < bytes+1) && (status == -1))
    switch state
        case 0 % check for block id
            b = data(n);
            n = n+1;
            if b == 0
                state = 1;
            elseif b == 1
                state = 2;
            elseif b == 165 %0xA5
                state = 3;
            end;
                
        case 1 % read time
            yr = data(n);
            mon = data(n+1);
            day = data(n+2);
            hr = data(n+3);
            min = data(n+4);
            sec = data(n+5);
            n = n+6;
            state = 0;
            
        case 2 % Read Block
            for i = 1:100
                for k = 1:12
                    d(k) = data(n+k-1);
                end;

                n = n+12;
                f1_u(blk*100+i) = d(1)*16+fix(d(3)/16);
                f2_u(blk*100+i) = d(2)*16+bitand(uint16(15),uint16(d(3)));
                
                f3_u(blk*100+i) = d(4)*16+fix(d(6)/16);
                f4_u(blk*100+i) = d(5)*16+bitand(uint16(15),uint16(d(6)));
                
                r1_u(blk*100+i) = d(7)*16+fix(d(9)/16);
                r2_u(blk*100+i) = d(8)*16+bitand(uint16(15),uint16(d(9)));
                
                r3_u(blk*100+i) = d(10)*16+fix(d(12)/16);
                r4_u(blk*100+i) = d(11)*16+bitand(uint16(15),uint16(d(12)));

                
                
                
            end;
            blk = blk+1;
            state = 0;
            
        case 3
            b = data(n);
            n = n+1;
            if b == 90
                status = 1;
            else
                status = 0;
            end;
    end;
            
end;

datetime = datenum(double(yr)+2000,double(mon),double(day),double(hr),double(min),double(sec));

f1 = double(f1_u(1:blk*100))*5/4096;
f2 = double(f2_u(1:blk*100))*5/4096;
f3 = double(f3_u(1:blk*100))*5/4096;
f4 = double(f4_u(1:blk*100))*5/4096;

r1 = double(r1_u(1:blk*100))*5/4096;
r2 = double(r2_u(1:blk*100))*5/4096;
r3 = double(r3_u(1:blk*100))*5/4096;
r4 = double(r4_u(1:blk*100))*5/4096;

len = length(f1);

fclose(fid);

	switch trans
	case 1
		display(f1)
	case 2
		display(f2)
	case 3
		display(f3)
	case 4
		display(f4)
	end
end
