#include <omp.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <math.h>

// Retirado e adaptado de https://stackoverflow.com/questions/54173733/convert-rgb-to-grayscale-in-bare-c
void imgToGray(FILE *fIn, FILE *fOut) {
  unsigned char header[54];
  fread(header, sizeof(unsigned char), 54, fIn);
  fwrite(header, sizeof(unsigned char), 54, fOut);

  int width = *(int*)&header[18];
  int height = abs(*(int*)&header[22]);
  int stride = (width * 3 + 3) & ~3;
  int padding = stride - width * 3;

  printf("width: %d (%d)\n", width, width * 3);
  printf("height: %d\n", height);
  printf("stride: %d\n", stride);
  printf("padding: %d\n", padding);

  unsigned char pixel[3];
  for (int y = 0; y < height; ++y)
    {
      for (int x = 0; x < width; ++x)
	{
	  fread(pixel, 3, 1, fIn);
	  unsigned char gray = pixel[0] * 0.3 + pixel[1] * 0.58 + pixel[2] * 0.11;
	  memset(pixel, gray, sizeof(pixel));
	  fwrite(&pixel, 3, 1, fOut);
	}
      fread(pixel, padding, 1, fIn);
      fwrite(pixel, padding, 1, fOut);
    }
  fclose(fOut);
  fclose(fIn);
}

void grayToSobel(FILE *fIn, FILE *fOut) {
  unsigned char header[54];
  fread(header, sizeof(unsigned char), 54, fIn);
  fwrite(header, sizeof(unsigned char), 54, fOut);

  int width = *(int*)&header[18];
  int height = abs(*(int*)&header[22]);
  int stride = (width * 3 + 3) & ~3;
  int padding = stride - width * 3;

  printf("width: %d (%d)\n", width, width * 3);
  printf("height: %d\n", height);
  printf("stride: %d\n", stride);
  printf("padding: %d\n", padding);

  int (*imgMatrix)[width] = malloc(sizeof(int[height][width]));
  unsigned char pixel[3];
  for (int y = 0; y < height; y++)
    {
      for (int x = 0; x < width; x++)
	{
	  fread(pixel, 3, 1, fIn);
	  imgMatrix[y][x]=(int)pixel[0];
	}
    };

  int (*gradX)[width] = malloc(sizeof(int[height][width]));
  for (int y = 1; y < height - 1; y++)
    {
      for (int x = 1; x < width - 1; x++)
	{
	  gradX[y][x] = (imgMatrix[y-1][x-1]*(-1))+(imgMatrix[y-1][x]*(-2))+(imgMatrix[y-1][x+1]*(-1))+(imgMatrix[y+1][x-1]*(1))+(imgMatrix[y+1][x]*(2))+(imgMatrix[y+1][x+1]*(1));
	}
    };
  
  int (*gradY)[width] = malloc(sizeof(int[height][width]));
  for (int y = 1; y < height - 1; y++)
    {
      for (int x = 1; x < width - 1; x++)
	{
	  gradY[y][x] = (imgMatrix[y-1][x-1]*(-1))+(imgMatrix[y][x-1]*(-2))+(imgMatrix[y+1][x-1]*(-1))+(imgMatrix[y-1][x+1]*(1))+(imgMatrix[y][x+1]*(2))+(imgMatrix[y+1][x+1]*(1));
	}
    };

  
  int (*sobelImg)[width] = malloc(sizeof(int[height][width]));
  for (int y = 1; y < height - 1; y++)
    {
      for (int x = 1; x < width - 1; x++)
	{
	  sobelImg[y][x] = (int)sqrt((pow(gradX[y][x], 2)) + (pow(gradY[y][x], 2)));
	  if (sobelImg[y][x] > 255) {
	    sobelImg[y][x] = 255;
	  }
	}
    };

  
  for (int y = 0; y < height; y++)
    {
      for (int x = 0; x < width; x++)
	{
	  fread(pixel, 3, 1, fIn);
	  unsigned char gradient = sobelImg[y][x];
	  memset(pixel, gradient, sizeof(pixel));
	  fwrite(&pixel, 3, 1, fOut);
	}
    
      fread(pixel, padding, 1, fIn);
      fwrite(pixel, padding, 1, fOut);
    }
  
  free(gradX);
  free(gradY);
  free(sobelImg);
  
  fclose(fOut);
  fclose(fIn);
}

void grayToSobelParalel(FILE *fIn, FILE *fOut) {
  unsigned char header[54];
  fread(header, sizeof(unsigned char), 54, fIn);
  fwrite(header, sizeof(unsigned char), 54, fOut);

  int width = *(int*)&header[18];
  int height = abs(*(int*)&header[22]);
  int stride = (width * 3 + 3) & ~3;
  int padding = stride - width * 3;

  printf("width: %d (%d)\n", width, width * 3);
  printf("height: %d\n", height);
  printf("stride: %d\n", stride);
  printf("padding: %d\n", padding);

  int (*imgMatrix)[width] = malloc(sizeof(int[height][width]));
  unsigned char pixel[3];
  for (int y = 0; y < height; y++)
    {
      for (int x = 0; x < width; x++)
	{
	  fread(pixel, 3, 1, fIn);
	  imgMatrix[y][x]=(int)pixel[0];
	}
    };

  
  int nthreads = omp_get_num_threads();
  int blockheight = height / nthreads;
  printf("Height of each block: %d\n", blockheight);
  
  int*** dividedMatrix = (int***)malloc(nthreads * sizeof(int**));
  
  if (dividedMatrix == NULL)
    {
      fprintf(stderr, "Out of memory");
      exit(0);
      }

  for (int i = 0; i < nthreads; i++)
    {
      dividedMatrix[i] = (int**)malloc(blockheight * sizeof(int*));
      
      if (dividedMatrix[i] == NULL)
	{
	  fprintf(stderr, "Out of memory");
	  exit(0);
	}
      
      for (int j = 0; j < blockheight; j++)
	{
	  dividedMatrix[i][j] = (int*)malloc(width * sizeof(int));
	  if (dividedMatrix[i][j] == NULL)
	    {
	      fprintf(stderr, "Out of memory");
	      exit(0);
	    }
	}
    };
    #pragma omp parallel for
    for (int z = 0; z < nthreads; z++) {
      for (int y = 0; y < blockheight; y++) {
	for (int x = 0; x < width; x++) {
	  dividedMatrix[z][y][x] = imgMatrix[y + blockheight*z][x];
	}
      }
    };

    int*** gradX = (int***)malloc(nthreads * sizeof(int**));

    if (gradX == NULL)
      {
	fprintf(stderr, "Out of memory");
	exit(0);
      }

    for (int i = 0; i < nthreads; i++)
      {
	gradX[i] = (int**)malloc(blockheight * sizeof(int*));

	if (gradX[i] == NULL)
	  {
	    fprintf(stderr, "Out of memory");
	    exit(0);
	  }

	for (int j = 0; j < blockheight; j++)
	  {
	    gradX[i][j] = (int*)malloc(width * sizeof(int));
	    if (gradX[i][j] == NULL)
	      {
		fprintf(stderr, "Out of memory");
		exit(0);
	      }
	  }
      }
      #pragma omp parallel for
      for (int z = 0; z < nthreads; z++){
	for (int y = 1; y < blockheight - 1; y++)
	  {
	    for (int x = 1; x < width - 1; x++)
	      {
		gradX[z][y][x] = (dividedMatrix[z][y-1][x-1]*(-1))+(dividedMatrix[z][y-1][x]*(-2))+(dividedMatrix[z][y-1][x+1]*(-1))+(dividedMatrix[z][y+1][x-1]*(1))+(dividedMatrix[z][y+1][x]*(2))+(dividedMatrix[z][y+1][x+1]*(1));
	      }
	  }
      };
    printf("Parallel GradX done\n");
    int*** gradY = (int***)malloc(nthreads * sizeof(int**));

    if (gradY == NULL)
      {
	fprintf(stderr, "Out of memory");
	exit(0);
      }

    for (int i = 0; i < nthreads; i++)
      {
	gradY[i] = (int**)malloc(blockheight * sizeof(int*));

	if (gradY[i] == NULL)
	  {
	    fprintf(stderr, "Out of memory");
	    exit(0);
	  }

	for (int j = 0; j < blockheight; j++)
	  {
	    gradY[i][j] = (int*)malloc(width * sizeof(int));
	    if (gradY[i][j] == NULL)
	      {
		fprintf(stderr, "Out of memory");
		exit(0);
	      }
	  }
      }

      #pragma omp for
      for (int z = 0; z < nthreads; z++) {
	for (int y = 1; y < blockheight - 1; y++)
	  {
	    for (int x = 1; x < width - 1; x++)
	      {
		gradY[z][y][x] = (dividedMatrix[z][y-1][x-1]*(-1))+(dividedMatrix[z][y][x-1]*(-2))+(dividedMatrix[z][y+1][x-1]*(-1))+(dividedMatrix[z][y-1][x+1]*(1))+(dividedMatrix[z][y][x+1]*(2))+(dividedMatrix[z][y+1][x+1]*(1));
	      }
	  }
      };
    printf("Parallel GradY done\n");
    int*** sobelImg = (int***)malloc(nthreads * sizeof(int**));

    if (sobelImg == NULL)
      {
	fprintf(stderr, "Out of memory");
	exit(0);
      }

    for (int i = 0; i < nthreads; i++)
      {
	sobelImg[i] = (int**)malloc(blockheight * sizeof(int*));

	if (sobelImg[i] == NULL)
	  {
	    fprintf(stderr, "Out of memory");
	    exit(0);
	  }

	for (int j = 0; j < blockheight; j++)
	  {
	    sobelImg[i][j] = (int*)malloc(width * sizeof(int));
	    if (sobelImg[i][j] == NULL)
	      {
		fprintf(stderr, "Out of memory");
		exit(0);
	      }
	  }
      }

      #pragma omp parallel for
      for (int z = 0; z < nthreads; z++) {
	for (int y = 1; y < blockheight - 1; y++)
	  {
	    for (int x = 1; x < width - 1; x++)
	      {
		sobelImg[z][y][x] = (int)sqrt((pow(gradX[z][y][x], 2)) + (pow(gradY[z][y][x], 2)));
		if (sobelImg[z][y][x] > 255) {
		  sobelImg[z][y][x] = 255;
		}
	      }
	  }
      };
    printf("Parallel SobelImg done\n");
    
    for (int z = 0; z < nthreads; z++) {
      for (int y = 0; y < height; y++)
	{
	  for (int x = 0; x < width; x++)
	    {
	      fread(pixel, 3, 1, fIn);
	      unsigned char gradient = sobelImg[z][y + blockheight*z][x];
	      memset(pixel, gradient, sizeof(pixel));
	      fwrite(&pixel, 3, 1, fOut);
	    }
	}
      
      fread(pixel, padding, 1, fIn);
      fwrite(pixel, padding, 1, fOut);
    };
    printf("Image put together\n");
    free(gradX);
    free(gradY);
    free(sobelImg);

  
  fclose(fOut);
  fclose(fIn);
}


int main(int argc, char* argv[]) {
  int nthreads, tid;
  
  FILE *fIn = fopen("Images/tank.bmp", "rb");
  FILE *fOut = fopen("Images/tank_gray.bmp", "wb");
  if (!fIn || !fOut) {
      printf("File error.\n");
      return 0;
  }
  
  imgToGray(fIn, fOut);
  
  FILE *fIn1 = fopen("Images/tank_gray.bmp", "rb");
  FILE *fOut1 = fopen("Images/tank_sobel.bmp", "wb");
  if (!fIn || !fOut) {
      printf("File error.\n");
      return 0;
  }
  
  clock_t begin = clock();

  grayToSobel(fIn1, fOut1);
  
  clock_t end = clock();
  double time_spent = (double)(end - begin) / CLOCKS_PER_SEC;

  FILE *fIn2 = fopen("Images/tank_gray.bmp", "rb");
  FILE *fOut2 = fopen("Images/tank_sobel_paralel.bmp", "wb");
  if (!fIn || !fOut) {
      printf("File error.\n");
      return 0;
  }
  
  clock_t begin2 = clock();

  grayToSobelParalel(fIn2, fOut2);
  
  clock_t end2 = clock();
  double time_spent2 = (double)(end - begin) / CLOCKS_PER_SEC;
  
  printf("It took %f seconds in sequential\n", time_spent);
  printf("It took %f seconds in paralel\n", time_spent2);

  double speedup = time_spent / time_spent2;

  printf("The speedup was %f\n", speedup);
  
}
