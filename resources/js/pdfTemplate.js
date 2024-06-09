import jsPDF from 'jspdf';
import 'jspdf-autotable';
import basmaLogo from '/resources/img/basma.png'; // Import the logo image
import { amiriFontBase64 } from '../fonts/amiri-regular'; // Import the Base64 font encoding

export { createPDFTemplate };

// Define a function to create a PDF template
const createPDFTemplate = (office, headline, bodyContent, t) => {
  const pdf = new jsPDF();

  const { tableHeaders, tableBody, adminSummary } = bodyContent;

  // Set up organization name
  const organizationName = t('user_list.pdf.organization_name');
  const officeText = `${office}`;

  const leftMargin = 10;
  const topMargin = 10;
  const logoWidth = 40;
  const logoHeight = 40;
  const organizationY = topMargin + 20;
  const officeY = organizationY + 10;
  const lineWidth = pdf.internal.pageSize.width - leftMargin * 2;
  const lineX = leftMargin;

  // Add the first logo on the left side
  pdf.addImage(basmaLogo, 'PNG', leftMargin, topMargin, logoWidth, logoHeight);

  // Add the second logo on the right side
  const rightMargin = pdf.internal.pageSize.width - logoWidth - 10;
  pdf.addImage(basmaLogo, 'PNG', rightMargin, topMargin, logoWidth, logoHeight);

  // Define font name and style
  const amiriFontName = 'Amiri';
  const amiriFontStyle = 'normal';

  // Add the font to jsPDF
  pdf.addFileToVFS('Amiri-Regular.ttf', amiriFontBase64);
  pdf.addFont('Amiri-Regular.ttf', 'Amiri', 'normal');
  pdf.setFont(amiriFontName, amiriFontStyle); // Use the font for the entire document

  // Add organization name
  pdf.setFontSize(14);
  const organizationNameWidth = pdf.getStringUnitWidth(organizationName) * 14 / pdf.internal.scaleFactor;
  const organizationX = (pdf.internal.pageSize.width - organizationNameWidth) / 2;
  pdf.text(organizationName, organizationX, organizationY);

  // Add office
  pdf.setFontSize(13);
  const officeTextWidth = pdf.getStringUnitWidth(officeText) * 10 / pdf.internal.scaleFactor;
  const officeX = (pdf.internal.pageSize.width - officeTextWidth) / 2;
  pdf.text(officeText, officeX, officeY);

  // Add horizontal line
  const dateY = topMargin + logoHeight + 20;
  pdf.setLineWidth(0.5);
  pdf.line(lineX, dateY, lineX + lineWidth, dateY);

  // Add date below the horizontal line
  const dateX = rightMargin;
  pdf.setFontSize(12);
  pdf.text(getCurrentDate(), dateX, dateY + 10);

  // Add headline
  pdf.setFontSize(13);
  pdf.text(headline, pdf.internal.pageSize.width / 2, dateY + 15, { align: 'center' });

  // Add body content
  pdf.setFontSize(12);

  // Check if table data is provided
  if (tableHeaders && tableBody) {
    pdf.autoTable({
      startY: dateY + 30,
      head: [tableHeaders],
      body: tableBody,
      margin: { left: leftMargin },
      styles: { font: amiriFontName } // Ensure the font is applied to the table
    });
  } else {
    pdf.text(bodyContent, leftMargin, dateY + 30);
  }

  // Add admin summary if provided
  if (adminSummary) {
    pdf.text(adminSummary, leftMargin, pdf.autoTable.previous.finalY + 10);
  }

  return pdf;
};

// Helper function to get current date in yyyy-mm-dd format
const getCurrentDate = () => {
  const date = new Date();
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};
