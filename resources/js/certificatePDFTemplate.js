import jsPDF from 'jspdf';
import 'jspdf-autotable';
import basmaLogo from '../img/basma.png'; // Logo image imported
import stampImage from '../img/basma.png'; // Stamp image imported
import signatureImage from '../img/basma.png'; // Signature image imported
import { amiriFontBase64 } from '../fonts/amiri-regular'; // Base64 encoded Amiri font imported
import { formatDate } from './Functions'; // Function for date formatting imported

// Exporting the function to create a PDF certificate template
export { createPDFTemplate };

/**
 * Function to create a PDF certificate template.
 * @param {Object} certificate - Certificate data object.
 * @param {Object} user - User data object.
 * @param {Function} t - Translation function from Vue I18n.
 * @param {string} bossName - Translated name of the organization's head.
 * @returns {jsPDF} - Generated PDF instance.
 */
const createPDFTemplate = (certificate, user, t, bossName) => {
  const pdf = new jsPDF();

  // Extract necessary data from translations and certificate/user data
  const organizationName = t('certificate.view.organization');
  const issueDate = formatDate(certificate.updated_at, t);
  const headline = t('certificate.view.volunteer_certificate');
  const bodyContent = t('certificate.view.certificate_text', {
    userName: user.name,
    joinDate: formatDate(user.join_date, t),
    goneDate: user.gone_at ? formatDate(user.gone_at, t) : formatDate(certificate.created_at, t),
  });
  const organizationBoss = t('organization_boss');
  const verificationLinkText = t('certificate.view.verify');
  const verificationLink = certificate.verification_link;
  const recognizesService = t('certificate.view.recognizes_service');

  const pageWidth = pdf.internal.pageSize.width;
  const pageHeight = pdf.internal.pageSize.height;

  // Ensure Amiri font is loaded and set as the font for the PDF
  pdf.addFileToVFS('amiri-regular.ttf', amiriFontBase64);
  pdf.addFont('amiri-regular.ttf', 'amiri', 'normal');
  pdf.setFont('amiri');

  // Calculate logo dimensions and positions
  const logoSize = 40;
  const logoMargin = 10;
  const logoY = 20;
  const logoXLeft = logoMargin;
  const logoXRight = pageWidth - logoMargin - logoSize;

  // Add logos to the PDF
  pdf.addImage(basmaLogo, 'PNG', logoXLeft, logoY, logoSize, logoSize);
  pdf.addImage(basmaLogo, 'PNG', logoXRight, logoY, logoSize, logoSize);

  // Add organization name centered between logos
  const organizationNameFontSize = 14;
  pdf.setFontSize(organizationNameFontSize);
  const organizationNameWidth = pdf.getStringUnitWidth(organizationName) * organizationNameFontSize / pdf.internal.scaleFactor;
  const organizationNameX = (pageWidth - organizationNameWidth) / 2;
  const organizationNameY = logoY + logoSize - 25; // Adjusted vertical position
  pdf.text(organizationName, organizationNameX, organizationNameY);

  // Add a blank line before the horizontal line separator
  const blankLineY = organizationNameY + 20; // Adjusted vertical position
  pdf.text('', logoXLeft, blankLineY);

  // Add horizontal line below organization name
  const lineY = blankLineY + 10;
  pdf.setLineWidth(0.5);
  pdf.line(logoXLeft, lineY, logoXRight + logoSize, lineY);

  // Add issue date
  const dateFontSize = 12;
  pdf.setFontSize(dateFontSize);
  const dateY = lineY + 10;
  pdf.text(issueDate, logoXRight, dateY, { align: 'right' });

  // Add headline
  const headlineFontSize = 16;
  pdf.setFontSize(headlineFontSize);
  const headlineY = dateY + 15;
  pdf.text(headline, pageWidth / 2, headlineY, { align: 'center' });

  // Add certificate content
  const contentFontSize = 12;
  pdf.setFontSize(contentFontSize);
  const contentY = headlineY + 20;
  const contentLines = pdf.splitTextToSize(bodyContent, pageWidth - 2 * logoMargin);
  
  // Check if the content is in Arabic to adjust alignment
  const isArabic = /[\u0600-\u06FF]/.test(bodyContent);

  // Add text with appropriate alignment and direction
  if (isArabic) {
    pdf.text(contentLines, pageWidth - logoMargin, contentY, { align: 'right', lang: 'ar' });
  } else {
    pdf.text(contentLines, logoMargin, contentY);
  }

  // Calculate height of contentLines to determine spacing
  const contentHeight = pdf.getTextDimensions(contentLines).h;

  // Add spacing between content and recognizesService
  const spacing = 10;
  const recognizesServiceY = contentY + contentHeight + spacing;
  
  // Add recognizesService text with appropriate alignment
  if (isArabic) {
    pdf.text(recognizesService, pageWidth - logoMargin, recognizesServiceY, { align: 'right', lang: 'ar' });
  } else {
    pdf.text(recognizesService, logoMargin, recognizesServiceY);
  }

  // Add stamp, organization boss name, and signature before the bottom horizontal line
  const stampSize = 30;
  const stampX = logoMargin;
  const stampY = pageHeight - 40 - stampSize;

  const signatureWidth = 50;
  const signatureHeight = 20;
  const signatureX = pageWidth - logoMargin - signatureWidth;
  const signatureY = stampY;

  pdf.addImage(stampImage, 'PNG', stampX, stampY, stampSize, stampSize);
  pdf.text(organizationBoss, signatureX, signatureY - 5); // Add organization boss name
  pdf.text(bossName, signatureX, signatureY + 3); // Add translated boss name
  pdf.addImage(signatureImage, 'PNG', signatureX, signatureY + 3, signatureWidth, signatureHeight); // Add signature image

  // Add horizontal line at the bottom of the page
  pdf.setLineWidth(0.5);
  pdf.line(logoXLeft, pageHeight - 20, logoXRight + logoSize, pageHeight - 20);
  
  // Add verification link at the bottom
  const bottomMargin = 15;
  const textX = logoMargin;
  const textY = pageHeight - bottomMargin;
  pdf.setFontSize(10);
  
  // Add verification link text with appropriate alignment
  if (isArabic) {
    pdf.text(`${verificationLinkText} ${verificationLink}`, pageWidth - logoMargin, textY, { align: 'right', lang: 'ar' });
  } else {
    pdf.text(`${verificationLinkText} ${verificationLink}`, textX, textY);
  }

  return pdf; // Return the generated PDF instance
};