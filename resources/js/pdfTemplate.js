import jsPDF from 'jspdf';
import basmaLogo from '/resources/img/basma.png'; // Import the logo image

export { createPDFTemplate };

// Define a function to create a PDF template
const createPDFTemplate = (office, headline, $body) => {
    const pdf = new jsPDF();
    
    // Set up organization name
    const organizationName = "Basma Charity Organization";
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
    pdf.setFont('helvetica', 'bold');
    pdf.text(headline, (pdf.internal.pageSize.width / 2), dateY + 15, { align: 'center' });

    // Add body content
    pdf.setFontSize(12);
    pdf.setFont('helvetica', 'normal');
    pdf.text($body, leftMargin, dateY + 30);

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
